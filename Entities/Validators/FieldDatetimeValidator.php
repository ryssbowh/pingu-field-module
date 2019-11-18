<?php

namespace Pingu\Field\Entities\Validators;

use Pingu\Field\Support\FieldValidator\BundleFieldFieldsValidator;

class FieldDatetimeValidator extends BundleFieldFieldsValidator
{
    /**
     * @inheritDoc
     */
    protected function rules(): array
    {
        return [
            'required' => 'boolean',
            'setToCurrent' => 'boolean',
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