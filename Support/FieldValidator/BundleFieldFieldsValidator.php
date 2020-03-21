<?php

namespace Pingu\Field\Support\FieldValidator;

use Pingu\Field\Contracts\BundleFieldContract;
use Pingu\Field\Entities\BundleField;

abstract class BundleFieldFieldsValidator extends BaseFieldsValidator
{
    public function __construct(BundleFieldContract $field)
    {
        parent::__construct($field);
    }

    /**
     * Returns the bundle field validator instance for the associated object
     * or a new instance of a BundleField validator
     * 
     * @return FieldsValidator
     */
    protected function getBundleFieldValidator()
    {
        if ($this->object->field) {
            return $this->object->field->validator();
        } else {
            return (new BundleField)->validator();
        }
    }

    /**
     * @inheritDoc
     */
    protected function buildMessages(): array
    {
        $generic = $this->object->field;
        return array_merge(parent::buildMessages(), $this->getBundleFieldValidator()->buildMessages());
    }
        
    /**
     * @inheritDoc
     */
    protected function buildRules(bool $updating): array
    {
        $generic = $this->object->field;
        return array_merge(parent::buildRules($updating), $this->getBundleFieldValidator()->buildRules($updating));
    }
}