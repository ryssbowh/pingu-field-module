<?php

namespace Pingu\Field\Entities\Validators;

use Pingu\Field\Support\FieldValidator\BundleFieldFieldsValidator;

class FieldDateValidator extends BundleFieldFieldsValidator
{
    /**
     * @inheritDoc
     */
    protected function rules(bool $updating): array
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