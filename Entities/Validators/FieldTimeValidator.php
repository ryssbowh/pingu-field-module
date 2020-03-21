<?php

namespace Pingu\Field\Entities\Validators;

use Pingu\Field\Support\FieldValidator\BaseFieldsValidator;

class FieldTimeValidator extends BaseFieldsValidator
{
    protected function rules(bool $updating): array
    {
        return [
            'required' => 'boolean',
            'setToCurrent' => 'boolean'
        ];
    }

    protected function messages(): array
    {
        return [

        ];
    }
}