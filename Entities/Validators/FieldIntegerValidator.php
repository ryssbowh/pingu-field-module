<?php

namespace Pingu\Field\Entities\Validators;

use Pingu\Field\Support\FieldValidator\BundleFieldFieldsValidator;

class FieldIntegerValidator extends BundleFieldFieldsValidator
{
    /**
     * @inheritDoc
     */
    protected function rules(): array
    {
        return [
            'default' => 'string',
            'required' => 'boolean'
        ];
    }

    /**
     * @inheritDoc
     */
    protected function messages(): array
    {
        return [];
    }
}