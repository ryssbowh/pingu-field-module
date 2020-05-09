<?php

namespace Pingu\Field\Support\FieldRepository;

use Illuminate\Support\Collection;
use Pingu\Field\Contracts\BundleFieldContract;
use Pingu\Field\Entities\BundleField;
use Pingu\Field\Exceptions\BundleFieldException;
use Pingu\Field\Support\FieldRepository\BaseFieldRepository;

/**
 * Defines a list of fields for a bundle field
 */
abstract class BundleFieldFieldRepository extends BaseFieldRepository
{
    public function __construct(BundleFieldContract $object)
    {
        parent::__construct($object);
    }

    /**
     * @inheritDoc
     */
    protected function buildFields(): Collection
    {
        $genericFields = (new BundleField)->fieldRepository()->all();
        $fields = parent::buildFields();
        foreach ($genericFields as $field) {
            if ($fields->has($field->machineName())) {
                throw BundleFieldException::nameReserved($field->machineName(), $this->object);
            }
        }
        return $genericFields->merge($fields);
    }

    /**
     * @inheritDoc
     */
    protected function buildMessages(): Collection
    {
        return parent::buildMessages()->merge((new BundleField)->fieldRepository()->validationMessages());
    }
        
    /**
     * @inheritDoc
     */
    protected function buildRules(): Collection
    {
        return parent::buildRules()->merge((new BundleField)->fieldRepository()->validationRules());
    }
}