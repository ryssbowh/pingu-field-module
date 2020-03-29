<?php

namespace Pingu\Field\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;
use Pingu\Entity\Traits\HasFieldDisplay;
use Pingu\Entity\Traits\HasFieldLayout;
use Pingu\Field\Entities\BundleField;
use Pingu\Field\Entities\BundleFieldValue;
use Pingu\Field\Support\FieldValuesRepository;

trait HasBundleFields
{
    use HasFieldLayout, 
        HasFieldDisplay;

    public $fieldValues;

    public function initializeHasBundleFields()
    {
        $this->fieldValues = new FieldValuesRepository($this);
    }

    /**
     * Boot this trait,
     * load the bundle field values for this entity when the model is retrieved.
     * deletes all values for this entity when the model is deleted.
     */
    public static function bootHasBundleFields()
    {
        static::retrieved(
            function ($entity) {
                $entity->fieldValues->load();
            }
        );

        static::deleted(
            function ($entity) {
                if (method_exists($entity, 'isForceDeleting') and !$entity->isForceDeleting()) {
                    $entity->fieldValues->delete();
                } else {
                    $entity->fieldValues->forceDelete();
                }
            }
        );
    }

    /**
     * value relation
     * 
     * @return MorphMany
     */
    public function values(): MorphMany
    {
        return $this->morphMany(BundleFieldValue::class, 'entity');
    }

    /**
     * @inheritDoc
     */
    public function getAttribute($key)
    {
        if (Str::startsWith($key, 'field_')) {
            return $this->fieldValues->getValue($key);
        }
        return parent::getAttribute($key);
    }

    /**
     * @inheritDoc
     */
    public function getAttributes()
    {
        return array_merge(parent::getAttributes(), $this->fieldValues->getRawValues());
    }

    /**
     * @inheritDoc
     */
    public function getOriginal($key = null, $default = null)
    {
        return array_merge(parent::getOriginal($key, $default), $this->fieldValues->getOriginal($key, $default));
    }

    /**
     * @inheritDoc
     */
    public function setAttribute($key, $value)
    {
        if (Str::startsWith($key, 'field_')) {
            $this->fieldValues->setValue($key, $value);
            return $this;
        }
        return parent::setAttribute($key, $value);
    }

    /**
     * @inheritDoc
     */
    public function wasChanged($attributes = null)
    {
        return (parent::wasChanged($attributes) or $this->fieldValues->wasChanged($attributes));
    }

    /**
     * @inheritDoc
     */
    public function isDirty($attributes = null)
    {
        $attributes = is_array($attributes) ? $attributes : func_get_args();
        return (parent::isDirty($attributes) or $this->fieldValues->isDirty($attributes));
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        $array = parent::toArray();
        if ($this->fieldValues) {
            $array = array_merge($array, $this->fieldValues->toArray());
        }
        return $array;
    }

    /**
     * @inheritDoc
     */
    public function getFillable()
    {
        if ($bundle = $this->bundle()) {
            return array_merge(parent::getFillable(), $bundle->fields()->allNames());
        }
        return parent::getFillable();
    }

    /**
     * @inheritDoc
     */
    protected function finishSave(array $options)
    {
        $this->fieldValues->save();
        parent::finishSave($options);
    }

    /**
     * Get the value of the model's route key taking into account bundle fields
     * will always return the first value of the bundle field
     *
     * @return mixed
     */
    public function getRouteKey()
    {
        $routeKey = parent::getRouteKey();
        $keyName = $this->getRouteKeyName();
        if (substr($keyName, 0, 6) == 'field_') {
            return $routeKey[0];
        }
        return $routeKey;
    }

    /**
     * Retrieve the model for a bound value taking into account bundle fields
     *
     * @param  mixed $value
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function resolveRouteBinding($value)
    {
        $keyName = $this->getRouteKeyName();
        if (substr($keyName, 0, 6) == 'field_') {
            $fieldName = substr($keyName, 6);
            $fieldValue = BundleFieldValue::where('entity_type', get_class($this))
                ->where('value', $value)
                ->whereHas(
                    'field', function (Builder $query) use ($fieldName) {
                        $query->where('machineName', $fieldName);
                    }
                )->first();
            
            return $fieldValue ? $fieldValue->entity : null;

        }
        return parent::resolveRouteBinding($value);
    }

    /**
     * @inheritDoc
     */
    protected static function getDefaultFriendlyFieldName($key): string
    {
        if (Str::startsWith($key, 'field_')) {
            $key = substr($key, 6);
        }
        return friendly_field_name($key);
    }
}