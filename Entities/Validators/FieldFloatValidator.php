<?php

namespace Pingu\Field\Entities\Validators;

use Pingu\Field\Support\FieldValidator\BundleFieldFieldsValidator;

class FieldFloatValidator extends BundleFieldFieldsValidator
{
    /**
     * @inheritDoc
     */
    protected function rules(): array
    {
        return [
            'precision' => 'integer|min:1',
            'default' => 'numeric',
            'required' => 'boolean'
        ];
    }

    /**
     * @inheritDoc
     */
    protected function messages(): array
    {
        return [
            'precision.required' => 'Precision is required'
        ];
    }
}