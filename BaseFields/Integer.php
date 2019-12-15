<?php

namespace Pingu\Field\BaseFields;

use Pingu\Field\Support\BaseField;
use Pingu\Forms\Support\Fields\NumberInput;

class Integer extends BaseField
{
    protected static $availableWidgets = [NumberInput::class];

    /**
     * @inheritDoc
     */
    public function defaultValidationRules(): array
    {
        return [$this->machineName => 'integer'];
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