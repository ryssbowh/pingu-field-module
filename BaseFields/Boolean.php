<?php

namespace Pingu\Field\BaseFields;

use Illuminate\Database\Eloquent\Builder;
use Pingu\Core\Entities\BaseModel;
use Pingu\Field\Displayers\FakeDisplayer;
use Pingu\Field\Support\BaseField;
use Pingu\Forms\Support\Fields\Checkbox;

class Boolean extends BaseField
{
    protected static $availableWidgets = [Checkbox::class];
    
    protected static $availableFilterWidgets = [Checkbox::class];

    protected static $displayers = [FakeDisplayer::class];

    /**
     * @inheritDoc
     */
    public function filterQueryModifier(Builder $query, $value, BaseModel $model)
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
    public function castToFormValue($value)
    {
        return (bool)$value;
    }

    /**
     * @inheritDoc
     */
    public function castValue($value)
    {
        return (bool)$value;
    }
}