<?php

namespace Pingu\Field\Support\FieldRepository;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Pingu\Entity\Contracts\BundleContract;
use Pingu\Field\Contracts\FieldRepository;
use Pingu\Field\Entities\BundleField;

/**
 * Defines a list of fields for a bundle
 */
class BundleFieldsRepository extends FieldRepository
{
    /**
     * Constructor, will build the fields and saves them in Cache
     * 
     * @param BundleContract $object
     */
    public function __construct(BundleContract $object)
    {
        $this->object = $object;
    }

    /**
     * @inheritDoc
     */
    protected function buildFields(): Collection
    {
        if (!Schema::hasTable('bundle_fields')) {
            return collect();
        }

        $fields = [];
        $bundleFields = BundleField::where(['bundle' => $this->object->bundleName()])->get();
        foreach ($bundleFields as $field) {
            $fields[$field->machineName] = $field->instance;
        }
        return collect($fields);
    }

    protected function getObjectCacheTarget()
    {
        return $this->object->bundleName();
    }

    /**
     * Returns bundle fields, including the entity base fields
     * 
     * @return Collection
     */
    public function getAll(): Collection
    {
        $entity = $this->object->entityFor();
        $entity = new $entity;
        $entity->setBundle($this->object);
        return $entity->fields()->get();
    }
}