<?php

namespace Pingu\Field\Support\FieldRepository;

use Illuminate\Support\Collection;
use Pingu\Field\Contracts\BundleFieldContract;
use Pingu\Field\Entities\BundleField;
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
        if ($this->object->field) {
            $fields = $this->object->field->fields()->get();
        } else {
            $fields = (new BundleField)->fields()->get();
        }
        return $fields->merge(parent::buildFields());
    }
}