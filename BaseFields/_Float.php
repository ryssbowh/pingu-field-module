<?php

namespace Pingu\Field\BaseFields;

use Illuminate\Database\Eloquent\Builder;
use Pingu\Core\Entities\BaseModel;
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

    /**
     * @inheritDoc
     */
    public function filterQueryModifier(Builder $query, $value, BaseModel $model)
    {
        $query->where($this->machineName, '=', $value);
    }
}