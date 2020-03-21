<?php

namespace Pingu\Field\Support\FieldValidator;

use Pingu\Entity\Contracts\BundleContract;
use Pingu\Field\Contracts\FieldsValidator;
use Pingu\Field\Entities\BundleField;

/**
 * Validator for a repository of bundle fields
 */
class BundleFieldsValidator extends FieldsValidator
{
    public function __construct(BundleContract $bundle)
    {
        parent::__construct($bundle);
    }

    /**
     * @inheritDoc
     */
    protected function buildMessages(): array
    {
        return $this->defaultFieldsMessages();
    }
        
    /**
     * @inheritDoc
     */
    protected function buildRules(bool $updating): array
    {
        return $this->defaultFieldsRules();
    }

    /**
     * Builds the rules for an object's fields
     * 
     * @return array
     */
    protected function defaultFieldsRules(): array
    {
        $out = [];
        foreach ($this->object->fields()->get() as $field) {
            $rules = $field->defaultValidationRules();
            $out = array_merge($out, $rules);
        }
        return $out;
    }

    /**
     * Builds the rules for an object's fields 
     * 
     * @return array
     */
    protected function defaultFieldsMessages(): array
    {
        $out = [];
        foreach ($this->object->fields()->get() as $field) {
            $messages = $field->defaultValidationMessages();
            $out = array_merge($out, $messages);
        }
        return $out;
    }
}