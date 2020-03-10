<?php

namespace Pingu\Field\Entities\Validators;

use Pingu\Field\Support\FieldValidator\BundleFieldFieldsValidator;

class FieldEntityValidator extends BundleFieldFieldsValidator
{
    /**
     * @inheritDoc
     */
    protected function rules(): array
    {
        return [
            'required' => 'boolean',
            'entity' => 'required|string|in:'.implode(',', $this->getEntities())
        ];
    }

    /**
     * @inheritDoc
     */
    protected function messages(): array
    {
        return [];
    }

    protected function getEntities()
    {
        return array_map(function ($entity) {
            return get_class($entity);
        }, \Entity::getRegisteredEntities());
    }
}