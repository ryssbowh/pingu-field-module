<?php

namespace Pingu\Field\Entities;

use Illuminate\Database\Eloquent\Builder;
use Pingu\Core\Entities\BaseModel;
use Pingu\Entity\Support\Entity;
use Pingu\Field\Displayers\DefaultFloatDisplayer;
use Pingu\Forms\Support\Field;
use Pingu\Forms\Support\Fields\NumberInput;

class FieldFloat extends BaseBundleField
{
    protected static $availableWidgets = [NumberInput::class];
    
    protected static $availableFilterWidgets = [NumberInput::class];

    protected static $displayers = [DefaultFloatDisplayer::class];

    protected $fillable = ['precision', 'default', 'required'];

    protected $casts = [
        'required' => 'boolean'
    ];

    protected $attributes = [
        'default' => ''
    ];

    /**
     * @inheritDoc
     */
    public function defaultValue()
    {
        return $this->default;
    }

    /**
     * @inheritDoc
     */
    public function uncastSingleValue($value)
    {
        return $value;
    }

    /**
     * @inheritDoc
     */
    public function castToSingleFormValue($value)
    {
        return $value;
    }

    /**
     * @inheritDoc
     */
    public function castSingleValue($value)
    {
        return $value;
    }

    /**
     * @inheritDoc
     */
    public function castSingleValueFromDb($value)
    {
        return $value === '' ? null : (float)$value;
    }

    /**
     * @inheritDoc
     */
    public function toSingleDbValue($value)
    {
        return $value;
    }

    /**
     * @inheritDoc
     */
    public function formFieldOptions(int $index = 0): array
    {
        return [
            'default' => $value ?? $this->default,
            'required' => $this->required
        ];
    }

    /**
     * @inheritDoc
     */
    public function defaultValidationRule(): string
    {
        return ($this->required ? 'required|' : '') . 'numeric';
    }

    /**
     * @inheritDoc
     */
    public function singleFilterQueryModifier(Builder $query, $value, BaseModel $model)
    {
        $query->where('value', '=', $value);
    }
}
