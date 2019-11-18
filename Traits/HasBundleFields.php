<?php

namespace Pingu\Field\Traits;

use Illuminate\Support\Str;
use Pingu\Field\Support\RevisionRepository;

trait HasBundleFields
{
    public $fieldRevisions;

    public function initializeHasBundleFields()
    {
        $this->fieldRevisions = new RevisionRepository($this);
    }

    /**
     * Boot this trait,
     * load the revisions for this entity when the model is retrieved.
     * deletes all values for this entity when the model is deleted.
     */
    public static function bootHasBundleFields()
    {
        static::retrieved(
            function ($entity) {
                $entity->fieldRevisions->load();
            }
        );
        static::deleted(
            function ($entity) {
                $entity->fieldRevisions->destroy();
            }
        );
    }

    /**
     * @inheritDoc
     */
    public function getAttribute($key)
    {
        if (Str::startsWith($key, 'field_')) {
            return $this->fieldRevisions->getValue($key);
        }
        return parent::getAttribute($key);
    }

    /**
     * @inheritDoc
     */
    public function setAttribute($key, $value)
    {
        if (Str::startsWith($key, 'field_')) {
            $this->fieldRevisions->setValue($key, $value);
            return $this;
        }
        return parent::setAttribute($key, $value);
    }

    /**
     * @inheritDoc
     */
    public function wasChanged($attributes = null)
    {
        return (parent::wasChanged($attributes) or $this->fieldRevisions->wasChanged($attributes));
    }

    /**
     * @inheritDoc
     */
    public function isDirty($attributes = null)
    {
        return (parent::isDirty($attributes) or $this->fieldRevisions->isDirty($attributes));
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        $array = parent::toArray();
        if ($this->fieldRevisions) {
            $array = array_merge($array, $this->fieldRevisions->toArray());
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
        $this->fieldRevisions->save();
        parent::finishSave($options);
    }
}