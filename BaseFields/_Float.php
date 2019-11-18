<?php

namespace Pingu\Field\BaseFields;

use Pingu\Field\Support\BaseField;
use Pingu\Forms\Support\Fields\NumberInput;

class _Float extends BaseField
{
    /**
     * @inheritDoc
     */
    protected function defaultFormFieldClass(): string
    {
        return NumberInput::class;
    }

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