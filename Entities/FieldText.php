<?php

namespace Pingu\Field\Entities;

use Pingu\Forms\Support\Field;
use Pingu\Forms\Support\Fields\TextInput;

class FieldText extends BaseBundleField
{
    protected static $availableWidgets = [TextInput::class];
    
    protected $fillable = ['default', 'required', 'maxLength'];

    protected $casts = [
        'required' => 'boolean'
    ];

    protected $attributes = [
        'default' => '',
        'maxLength' => 255
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
    public function castSingleValue($value)
    {
        return $value;
    }

    /**
     * @inheritDoc
     */
    public function singleFormValue($value)
    {
        return $value;
    }

    /**
     * @inheritDoc
     */
    public function toSingleFormField($value): Field
    {
        return new TextInput(
            $this->machineName(),
            [
                'label' => false,
                'default' => $value,
                'required' => $this->required,
                'maxlength' => $this->maxLength
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function defaultValidationRule(): string
    {
        $rules = ['string|max:'.$this->maxLength];
        if ($this->required) {
            $rules[] = 'required';
        }
        return implode('|', $rules);
    }
}
