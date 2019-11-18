<?php

namespace Pingu\Field\BaseFields;

use Pingu\Forms\Support\Fields\Password as PasswordFormField;

class Password extends Text
{
    /**
     * @inheritDoc
     */
    protected function defaultFormFieldClass(): string
    {
        return PasswordFormField::class;
    }

    /**
     * @inheritDoc
     */
    public function defaultValidationRules(): array
    {
        return [$this->machineName => 'min:8|string'];
    }

    /**
     * @inheritDoc
     */
    public function castValue($value)
    {
        return $value;
    }

    /**
     * @inheritDoc
     */
    public function formValue($value)
    {
        return $value;
    }
}