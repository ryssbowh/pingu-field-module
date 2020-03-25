<?php

namespace Pingu\Field\BaseFields;

use Illuminate\Database\Eloquent\Builder;
use Pingu\Core\Entities\BaseModel;
use Pingu\Field\Displayers\DefaultTextDisplayer;
use Pingu\Field\Displayers\TrimmedTextDisplayer;
use Pingu\Field\Support\BaseField;
use Pingu\Forms\Support\Fields\TextInput;

class Text extends BaseField
{
    protected static $availableWidgets = [TextInput::class];

    protected static $availableFilterWidgets = [TextInput::class];

    protected static $displayers = [DefaultTextDisplayer::class, TrimmedTextDisplayer::class];

    /**
     * @inheritDoc
     */
    public function filterQueryModifier(Builder $query, $value, BaseModel $model)
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