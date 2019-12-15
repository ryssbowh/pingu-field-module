<?php

namespace Pingu\Field\BaseFields;

use Pingu\Field\Support\BaseField;
use Pingu\Forms\Support\Fields\Checkbox;

class Boolean extends BaseField
{
    protected static $availableWidgets = [Checkbox::class];

    /**
     * @inheritDoc
     */
    public function filterQueryModifier(Builder $query, $value)
    {
        if ($value) {
            $value = $value == 'true' ? 1 : 0;
            $query->where($this->machineName, '=', $value);
        }
    }

    /**
     * @inheritDoc
     */
    public function defaultValidationRules(): array
    {
        return [$this->machineName => 'boolean'];
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