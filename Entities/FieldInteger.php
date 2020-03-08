<?php

namespace Pingu\Field\Entities;

use Pingu\Forms\Support\Field;
use Pingu\Forms\Support\Fields\NumberInput;

class FieldInteger extends BaseBundleField
{
    protected static $availableWidgets = [NumberInput::class];
    
    protected $fillable = ['default', 'required'];

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
    public function castToSingleFormValue($value)
    {
        return $value;
    }

    /**
     * @inheritDoc
     */
    public function castSingleValueToDb($value)
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
        return (int)$value;
    }

    /**
     * @inheritDoc
     */
    public function toSingleFormField($value): Field
    {
        return new NumberInput(
            $this->machineName(),
            [
                'label' => $this->name(),
                'showLabel' => false,
                'default' => $value ?? $this->default,
                'required' => $this->required
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function defaultValidationRule(): string
    {
        return ($this->required ? 'required|' : '') . 'integer';
    }

}
