<?php

namespace Pingu\Field\BaseFields;

use Pingu\Field\Displayers\DefaultEmailDisplayer;
use Pingu\Forms\Support\Fields\Email as EmailFormField;
use Pingu\Forms\Support\Fields\TextInput;

class Email extends Text
{
    protected static $availableWidgets = [EmailFormField::class];

    protected static $availableFilterWidgets = [TextInput::class];

    protected static $displayers = [DefaultEmailDisplayer::class];

    /**
     * @inheritDoc
     */
    public function defaultValidationRules(): array
    {
        return [$this->machineName => 'email'];
    }
}