<?php

namespace Pingu\Field\BaseFields;

use Pingu\Forms\Support\Fields\Password as PasswordFormField;

class Password extends Text
{
    protected static $availableWidgets = [PasswordFormField::class];

    /**
     * @inheritDoc
     */
    public function defaultValidationRules(): array
    {
        return [$this->machineName => 'min:8|string'];
    }
}