<?php

namespace Pingu\Field\BaseFields;

use Illuminate\Database\Query\Builder;
use Pingu\Field\Support\BaseField;
use Pingu\Forms\Support\Fields\TextInput;

class Text extends BaseField
{
    protected static $availableWidgets = [TextInput::class];

    /**
     * @inheritDoc
     */
    public function filterQueryModifier(Builder $query, $value)
    {
        if ($value) {
            $query->where($this->machineName, 'like', '%'.$value.'%');
        }
    }

    /**
     * @inheritDoc
     */
    public function defaultValidationRules(): array
    {
        return [$this->machineName => 'string'];
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
    public function castToFormValue($value)
    {
        return $value;
    }
}