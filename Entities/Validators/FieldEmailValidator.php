<?php

namespace Pingu\Field\Entities\Validators;

use Pingu\Field\Support\FieldValidator\BundleFieldFieldsValidator;

class FieldEmailValidator extends BundleFieldFieldsValidator
{
    /**
     * @inheritDoc
     */
    protected function rules(): array
    {
        return [
            'default' => 'email',
            'required' => 'boolean'
        ];
    }

    /**
     * @inheritDoc
     */
    protected function messages(): array
    {
        return [
            'title.required' => 'Title is required'
        ];
    }
}