<?php

namespace Pingu\Field\Entities;

use Illuminate\Database\Eloquent\Builder;
use Pingu\Entity\Entities\Entity;
use Pingu\Forms\Support\Field;
use Pingu\Forms\Support\Fields\TextInput;

class FieldText extends BaseBundleField
{
    protected static $availableWidgets = [TextInput::class];
    
    protected static $availableFilterWidgets = [TextInput::class];
    
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
    public function castSingleValueToDb($value)
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
        return $value;
    }

    /**
     * @inheritDoc
     */
    public function formFieldOptions(int $index = 0): array
    {
        return [
            'required' => $this->required,
            'maxlength' => $this->maxLength
        ];
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

    /**
     * @inheritDoc
     */
    public function singleFilterQueryModifier(Builder $query, $value, Entity $entity)
    {
        $query->where('value', 'like', '%'.$value.'%');
    }
}
