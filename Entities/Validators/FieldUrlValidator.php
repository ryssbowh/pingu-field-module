<?php

namespace Pingu\Field\Entities\Validators;

use Pingu\Field\Support\FieldValidator\BundleFieldFieldsValidator;

class FieldUrlValidator extends BundleFieldFieldsValidator
{
    /**
     * @inheritDoc
     */
    protected function rules(): array
    {
        return [
            'default' => 'valid_url',
            'required' => 'boolean',
            'name' => 'required|string',
            'helper' => 'string',
        ];
    }

    /**
     * @inheritDoc
     */
    protected function messages(): array
    {
        return [
            'default.valid_url' => 'Default is not a valid url'
        ];
    }
}