<?php

namespace Pingu\Field\BaseFields;

use Pingu\Field\Support\BaseField;
use Pingu\Forms\Support\Fields\NumberInput;

class _Float extends BaseField
{
    protected static $availableWidgets = [NumberInput::class];
    
    protected static $availableFilterWidgets = [NumberInput::class];

    /**
     * @inheritDoc
     */
    public function defaultValidationRules(): array
    {
        return [$this->machineName => 'numeric'];
    }

    /**
     * @inheritDoc
     */
    public function castToFormValue($value)
    {
        return $value;
    }

    /**
     * @inheritDoc
     */
    public function castValue($value)
    {
        return $value;
    }
}