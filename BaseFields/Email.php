<?php

namespace Pingu\Field\BaseFields;

use Pingu\Forms\Support\Fields\Email as EmailFormField;
use Pingu\Forms\Support\Fields\TextInput;

class Email extends Text
{
    protected static $availableWidgets = [EmailFormField::class];

    protected static $availableFilterWidgets = [TextInput::class];

    /**
     * @inheritDoc
     */
    public function defaultValidationRules(): array
    {
        return [$this->machineName => 'email'];
    }
}