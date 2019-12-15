<?php

namespace Pingu\Field\Entities;

use Pingu\Forms\Support\Field;
use Pingu\Forms\Support\Fields\Checkbox;

class FieldBoolean extends BaseBundleField
{
    protected static $availableWidgets = [Checkbox::class];
    
    protected $fillable = ['default'];

    protected $casts = [
        'default' => 'boolean'
    ];

    protected $attributes = [
        'default' => false
    ];

    /**
     * @inheritDoc
     */
    public function defaultValue()
    {
        return (int)$this->default;
    }

    /**
     * @inheritDoc
     */
    public function singleFormValue($value)
    {
        return (int)$value;
    }

    /**
     * @inheritDoc
     */
    public function castSingleValue($value)
    {
        return (bool)$value;
    }
    
    /**
     * @inheritDoc
     */
    public function toSingleFormField($value): Field
    {
        return new Checkbox(
            $this->machineName(),
            [
                'label' => false,
                'default' => $value ?? $this->default
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function defaultValidationRule(): string
    {
        return 'boolean';
    }

}
