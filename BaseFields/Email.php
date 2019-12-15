<?php

namespace Pingu\Field\BaseFields;

use Pingu\Forms\Support\Fields\Email as EmailFormField;

class Email extends Text
{
    protected static $availableWidgets = [EmailFormField::class];

    /**
     * @inheritDoc
     */
    public function defaultValidationRules(): array
    {
        return [$this->machineName => 'email'];
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