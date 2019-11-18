<?php

namespace Pingu\Field\Entities;

use Pingu\Core\Entities\BaseModel;
use Pingu\Field\Contracts\BundleFieldContract;
use Pingu\Field\Traits\BundleField;
use Pingu\Forms\Support\Field;
use Pingu\Forms\Support\Fields\TextInput;

class FieldText extends BaseModel implements BundleFieldContract
{
    use BundleField;
    
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
    protected function castSingleValue($value)
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
                'default' => $value
            ],
            [
                'required' => $this->required,
                'maxlength' => $this->maxLength
            ]
        );
    }

    /**
     * @inheritDoc
     */
    protected function defaultValidationRule(): string
    {
        $rules = ['string|max:'.$this->maxLength];
        if ($this->required) {
            $rules[] = 'required';
        }
        return implode('|', $rules);
    }
}
