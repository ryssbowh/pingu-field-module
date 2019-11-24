<?php

namespace Pingu\Field\Entities;

use Pingu\Core\Entities\BaseModel;
use Pingu\Field\Contracts\BundleFieldContract;
use Pingu\Field\Traits\BundleField;
use Pingu\Forms\Support\Field;
use Pingu\Forms\Support\Fields\TextInput;

class FieldUrl extends BaseModel implements BundleFieldContract
{
    use BundleField;
    
    protected $fillable = ['required', 'default'];

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
                'default' => $value ?? $this->default,
                'required' => $this->required
            ]
        );
    }

    /**
     * @inheritDoc
     */
    protected function defaultValidationRule(): string
    {
        return 'string';
    }
}
