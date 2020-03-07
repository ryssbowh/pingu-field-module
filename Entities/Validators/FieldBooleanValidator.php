<?php

namespace Pingu\Field\Entities\Validators;

use Pingu\Field\Support\FieldValidator\BundleFieldFieldsValidator;

class FieldBooleanValidator extends BundleFieldFieldsValidator
{
    /**
     * @inheritDoc
     */
    protected function rules(): array
    {
        return [
            'default' => 'boolean'
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