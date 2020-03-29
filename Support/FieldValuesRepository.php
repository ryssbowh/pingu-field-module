<?php

namespace Pingu\Field\Support;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Pingu\Entity\Support\BundledEntity;
use Pingu\Field\Contracts\BundleFieldContract;
use Pingu\Field\Entities\BundleField;
use Pingu\Field\Entities\BundleFieldValue;
use Pingu\Field\Exceptions\RevisionException;

/**
 * Class designed to handle a set of revisions attached to an entity
 */
class FieldValuesRepository
{
   
    /**
     * BundleFieldValue Collection
     * 
     * @var Collection
     */
    protected $values;

    /**
     * @var BundledEntity
     */
    protected $entity;

    /**
     * Raw values, as saved in database
     * 
     * @var array
     */
    protected $rawValues = [];

    /**
     * Casted values, as used in entities
     * 
     * @var array
     */
    protected $castedValues = [];

    /**
     * Original values
     * 
     * @var array
     */
    protected $original = [];

    /**
     * Changed values
     * 
     * @var array
     */
    protected $changes = [];

    /**
     * Dirty values
     * 
     * @var array
     */
    protected $dirty = [];

    /**
     * Are the values loaded
     * 
     * @var boolean
     */
    protected $loaded = false;

    public function __construct(BundledEntity $entity)
    {
        $this->entity = $entity;
        $this->values = collect();
    }

    /**
     * Loads the revisions
     * 
     * @return FieldValuesRepository
     */
    public function load(): FieldValuesRepository
    {
        if ($this->loaded) {
            return $this;
        }
        $this->values = $this->resolveValues();
        $fields = $this->entity->bundle()->fields()->get();
        foreach ($fields as $name => $field) {
            $values = $this->values->where('field_id', $field->field->id);
            $fieldValue = $values->pluck('value')->toArray();
            $rawValue = $field->castValueFromDb($fieldValue);
            $this->rawValues[$name] = $rawValue;
        }
        $this->syncOriginal();
        $this->loaded = true;
        return $this;
    }

    /**
     * Sets the value of a field
     * 
     * @param string $name
     * @param mixed  $value
     *
     * @return FieldValuesRepository
     */
    public function setValue(string $name, array $value): FieldValuesRepository
    {
        $field = $this->entity->fields()->get($name);
        $value = Arr::wrap($value);
        $rawValue = $field->uncastValue($value);
        // dump($name);
        // dump($value);
        // dump($this->rawValues[$name] ?? null);
        // dump($rawValue);
        if (!isset($this->rawValues[$name]) or !$this->originalIsEquivalent($this->rawValues[$name], $rawValue)) {
            $this->rawValues[$name] = $rawValue;
            $this->dirty[$name] = $rawValue;
        }
        // dump($this->dirty);
        // dump('----------------------');
        return $this;
    }

    /**
     * Create a default value for a field
     * 
     * @param BundleField $field
     */
    public function createDefaultValue(BundleField $field)
    {
        $value = $this->createModel($field);
        $instance = $field->instance;
        $value->value = $instance->uncastSingleValue($instance->defaultValue());
        $value->save();
    }

    /**
     * Determine if the revision or any of the given attribute(s) have been modified.
     *
     * @param array|string|null $attributes
     * 
     * @return bool
     */
    public function isDirty($attributes = null): bool
    {
        return $this->hasChanges(
            $this->getDirty(), is_array($attributes) ? $attributes : []
        );
    }

    /**
     * Determine if the revision or any of the given attribute(s) have been modified.
     *
     * @param array|string|null $attributes
     * 
     * @return bool
     */
    public function wasChanged($attributes = null): bool
    {
        return $this->hasChanges(
            $this->getChanges(), is_array($attributes) ? $attributes : []
        );
    }

    /**
     * Get the revision as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        $out = [];
        foreach ($this->rawValues as $name => $value) {
            $out[$name] = $value;
        }
        return $out;
    }

    /**
     * Get all raw values
     * 
     * @return array
     */
    public function getRawValues(): array
    {
        return $this->rawValues;
    }

    /**
     * Get the attributes that have been changed
     * 
     * @return array
     */
    public function getChanges(): array
    {
        return $this->changes;
    }

    /**
     * Get the value of a field
     * 
     * @param string $name
     * 
     * @return mixed
     */
    public function getValue(string $name)
    {
        if (!isset($this->castedValues[$name])) {
            $field = $this->entity->fields()->get($name);
            $this->castedValues[$name] = $field->castValue($this->rawValues[$name]);
        }
        return $this->castedValues[$name];
    }

    /**
     * Get original attributes
     * 
     * @return array
     */
    public function getOriginal($key = null, $default = null): array
    {
        return Arr::get($this->original, $key, $default);
    }

    /**
     * Sync the original attributes with the current.
     *
     * @return FieldValuesRepository
     */
    public function syncOriginal(): FieldValuesRepository
    {
        $this->original = $this->rawValues;
        return $this;
    }

    /**
     * Sync changes with dirty attributes
     */
    public function syncChanges()
    {
        $this->changes = $this->getDirty();
    }

    /**
     * Get the attributes that have been changed since last sync.
     *
     * @return array
     */
    public function getDirty(): array
    {
        return $this->dirty;
    }

    /**
     * Deletes all values
     */
    public function delete()
    {
        foreach ($this->values as $model) {
            $model->delete();
        }
    }

    /**
     * Force delete all values
     */
    public function forceDelete()
    {
        foreach ($this->values as $model) {
            $model->forceDelete();
        }
    }

    /**
     * Saves field values
     * 
     * @return bool
     */
    public function save(): bool
    {
        $models = collect();
        $fields = $this->entity->bundle()->fields()->get();
        foreach ($this->getDirty() as $name => $values) {
            $field = $fields[$name]->field;
            $this->saveField($field, $values);
        }
        $this->syncChanges();
        $this->dirty = [];
        return true;
    }

    /**
     * Saves the values for one field
     * 
     * @param BundleField $field
     * @param array       $values
     */
    protected function saveField(BundleField $field, array $values)
    {
        $models = $this->values->where('field_id', $field->id)->values();
        $diff = $models->count() - count($values);
        if ($diff > 0) {
            $this->removeExtraModels($models, $diff);
        } elseif ($diff < 0) {
            $this->addExtraModels($models, $diff * -1, $field);
        }
        $values = array_values($values);
        foreach ($models as $index => $model) {
            $model->value = $field->instance->toSingleDbValue($values[$index]);
            $model->save();
        }
    }

    /**
     * Deletes value models if there are too many 
     * to store the current value
     * 
     * @param Collection $models
     * @param int        $amount
     */
    protected function removeExtraModels(Collection $models, int $amount)
    {
        while ($amount > 0) {
            $models->pop()->forceDelete();
            $amount--;
        }
    }

    /**
     * Adds value models so there are the same 
     * amount as the current value
     * 
     * @param Collection  $models
     * @param int         $amount
     * @param BundleField $field
     */
    protected function addExtraModels(Collection $models, int $amount, BundleField $field)
    {
        while ($amount > 0) {
            $models->push($this->createModel($field));
            $amount--;
        }
    }

    /**
     * Creates a field value model
     * 
     * @param BundleField $field
     * @param int         $id
     * @param mixed       $value
     * 
     * @return BundleFieldValue
     */
    protected function createModel(BundleField $field): BundleFieldValue
    {
        $fieldValue = new BundleFieldValue;
        $fieldValue->field()->associate($field);
        $fieldValue->entity()->associate($this->entity);
        return $fieldValue;
    }

    /**
     * Get values from cache
     * 
     * @return Collection
     */
    protected function resolveValues(): Collection
    {
        $entity = $this->entity;
        return \Field::getBundleValuesCache(
            $this->entity, function () use ($entity) {
                return $entity->values; 
            }
        );
    }

    /**
     * Determine if a value is equivalent to the current value
     *
     * @param array $value
     * @param array $current
     * 
     * @return bool
     */
    protected function originalIsEquivalent(array $value, array $current): bool
    {
        if (sizeof($value) != sizeof($current)) {
            return false;
        }

        foreach ($value as $key => $singleValue) {
            $currentValue = $current[$key] ?? null;
            if ($currentValue instanceof Model and $singleValue instanceof Model and !$singleValue->is($current)) {
                return false;
            } elseif (is_numeric($currentValue) && is_numeric($singleValue) 
                && strcmp((string) $currentValue, (string) $singleValue) !== 0
            ) {
                return false;
            } elseif ($currentValue !== $singleValue) {
                return false;
            } 
        }

        return true;
    }

    /**
     * Determine if any of the given attributes were changed.
     *
     * @param array             $changes
     * @param array|string|null $attributes
     * 
     * @return bool
     */
    protected function hasChanges($changes, $attributes = null): bool
    {
        if (empty($attributes)) {
            return count($changes) > 0;
        }

        foreach (Arr::wrap($attributes) as $attribute) {
            if (array_key_exists($attribute, $changes)) {
                return true;
            }
        }

        return false;
    }
}