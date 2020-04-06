<?php

namespace Pingu\Field\BaseFields;

use Illuminate\Database\Eloquent\Builder;
use Pingu\Core\Entities\BaseModel;
use Pingu\Field\Displayers\DefaultIntegerDisplayer;
use Pingu\Field\Support\BaseField;
use Pingu\Forms\Support\Fields\NumberInput;

class Integer extends BaseField
{
    protected static $availableWidgets = [NumberInput::class];
    
    protected static $availableFilterWidgets = [NumberInput::class];

    protected static $displayers = [DefaultIntegerDisplayer::class];

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
    public function castToFormValue($value)
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