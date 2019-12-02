<?php 

namespace Pingu\Field\Support;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Pingu\Entity\Entities\Entity;
use Pingu\Field\Entities\BundleField;
use Pingu\Field\Entities\BundleFieldValue;
use Pingu\Field\Events\CreatingRevision;
use Pingu\Field\Events\RevisionCreated;

/**
 * Class to handle a set of values attached to an entity for a specific revision id
 */
class FieldRevision
{
    /**
     * BundleFieldValue Collection
     * 
     * @var Collection
     */
    protected $values;

    /**
     * Id of this revision
     * 
     * @var int
     */
    protected $id;

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

    public function __construct(Entity $entity, Collection $values, int $id)
    {
        $this->entity = $entity;
        $this->values = $values;
        $this->id = $id;
    }

    /**
     * Id for this revision
     * 
     * @return int
     */
    public function id(): int
    {
        return $this->id;
    }

    /**
     * loads the values of the associated entity
     * 
     * @return FieldRevision
     */
    public function load(): FieldRevision
    {
        if ($this->loaded) {
            return $this;
        }
        $this->values = $this->values->groupBy('field_id');
        foreach ($this->values as $values) {
            $field = $values[0]->field->instance;
            $fieldValue = $values->pluck('value')->toArray();
            $this->rawValues[$field->machineName()] = $field->formValue($fieldValue);
            $this->castedValues[$field->machineName()] = $field->castValue($fieldValue);
        }
        $this->syncOriginal();
        $this->loaded = true;
        return $this;
    }

    /**
     * Sync the original attributes with the current.
     *
     * @return $this
     */
    public function syncOriginal(): FieldRevision
    {
        $this->original = $this->rawValues;
        return $this;
    }

    /**
     * Get original attributes
     * 
     * @return array
     */
    public function getOriginal(): array
    {
        return $this->original;
    }

    /**
     * Sync the changed attributes.
     *
     * @return $this
     */
    public function syncChanges(): FieldRevision
    {
        $this->changes = $this->getDirty();
        $this->dirty = [];
        return $this;
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
     * Sets the value of a field of this revision
     * 
     * @param string $name
     * @param mixed $value
     *
     * @return FieldRevision
     */
    public function setValue(string $name, $value): FieldRevision
    {
        $field = $this->entity->fields()->get($name);
        $value = Arr::wrap($value);
        $rawValue = $field->formValue($value);
        if (!isset($this->rawValues[$name]) or !$this->originalIsEquivalent($this->rawValues[$name], $rawValue)) {
            $this->castedValues[$name] = $value;
            $this->rawValues[$name] = $rawValue;
            $this->dirty[$name] = $rawValue;
        }
        return $this;
    }

    /**
     * Get the value of a field in this revision
     * 
     * @param string $name
     * 
     * @return mixed
     */
    public function getValue(string $name)
    {
        return $this->castedValues[$name] ?? [];
    }

    /**
     * Get the raw value of a field in this revision
     * 
     * @param string $name
     * 
     * @return mixed
     */
    public function getRawValue(string $name)
    {
        return $this->rawValues[$name] ?? [];
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
     * Determine if the revision or any of the given attribute(s) have been modified.
     *
     * @param array|string|null  $attributes
     * 
     * @return bool
     */
    public function isDirty($attributes = null): bool
    {
        return $this->hasChanges(
            $this->getDirty(), is_array($attributes) ? $attributes : func_get_args()
        );
    }

    /**
     * Determine if the revision or any of the given attribute(s) have been modified.
     *
     * @param array|string|null  $attributes
     * 
     * @return bool
     */
    public function wasChanged($attributes = null): bool
    {
        return $this->hasChanges(
            $this->getChanges(), is_array($attributes) ? $attributes : func_get_args()
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
     * Deletes all the values of this revision
     * 
     * @return FieldRevision
     */
    public function delete(): FieldRevision
    {
        foreach ($this->values as $collection) {
            $collection->each(function ($value) {
                $value->delete();
            });
        }
        return $this;
    }

    /**
     * Checks if this revision is the same intance as another one
     * 
     * @param FieldRevision $revision
     * 
     * @return boolean                
     */
    public function is(FieldRevision $revision): bool
    {
        return $this->id === $revision->id();
    }

    /**
     * Saves this revision as a new revision with a new id, 
     * creating new values for all the values of this revision.
     * 
     * @param int    $newId
     * 
     * @return FieldRevision
     */
    public function saveAsNew(int $newId): FieldRevision
    {
        $models = collect();
        foreach ($this->entity->bundle()->fields()->get() as $field) {
            $values = $this->getRawValue($field->machineName());
            foreach ($values as $value) {
                $models->push($this->createModel($field->field, $newId, $value));
            }
        }
        event(new CreatingRevision($this->entity, $models, $newId));
        $models->each( function ($model) {
            $model->save();
        });
        $revision = new FieldRevision($this->entity, $models, $newId);
        $revision->load();
        event(new RevisionCreated($this->entity, $revision));
        return $revision;
    }

    /**
     * Get the entity associated to this revision
     * 
     * @return Entity
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Get all the values for a field
     * 
     * @param BundleField $field
     * 
     * @return array
     */
    // protected function getFieldValues(BundleField $field): array
    // {
    //     return $this->values->where('field_id', $field->id)->pluck('value')->toArray();
    // }

    /**
     * Creates a field value model
     * 
     * @param BundleField $field
     * @param int         $id
     * @param mixed       $value
     * 
     * @return BundleFieldValue
     */
    protected function createModel(BundleField $field, int $id, $value): BundleFieldValue
    {
        $fieldValue = new BundleFieldValue;
        $fieldValue->field()->associate($field);
        $fieldValue->entity()->associate($this->entity);
        $fieldValue->revision_id = $id;
        $fieldValue->value = $value;
        return $fieldValue;
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
     * @param array  $changes
     * @param array|string|null  $attributes
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