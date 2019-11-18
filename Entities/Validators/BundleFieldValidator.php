<?php

namespace Pingu\Field\Entities\Validators;

use Pingu\Field\Support\FieldValidator\BaseFieldsValidator;

class BundleFieldValidator extends BaseFieldsValidator
{
    /**
     * @inheritDoc
     */
    protected function rules(): array
    {
        return [
            'machineName' => 'required|string',
            'name' => 'required',
            'weight' => 'integer',
            'cardinality' => 'integer'
        ];
    }

    /**
     * @inheritDoc
     */
    protected function messages(): array
    {
        return [
            'machineName.required' => 'Machine name is required'
        ];
    }
}