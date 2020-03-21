<?php

namespace Pingu\Field\Entities\Validators;

use Pingu\Field\Support\FieldValidator\BaseFieldsValidator;

class BundleFieldValidator extends BaseFieldsValidator
{
    /**
     * @inheritDoc
     */
    protected function rules(bool $updating): array
    {
        return [
            'machineName' => $updating ? null : 'required|string',
            'name' => 'required|string',
            'cardinality' => 'integer',
            'helper' => 'string'
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