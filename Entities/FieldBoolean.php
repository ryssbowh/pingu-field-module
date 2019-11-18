<?php

namespace Pingu\Field\Entities;

use Pingu\Core\Entities\BaseModel;
use Pingu\Field\Contracts\BundleFieldContract;
use Pingu\Field\Traits\BundleField;
use Pingu\Forms\Support\Field;
use Pingu\Forms\Support\Fields\Checkbox;

class FieldBoolean extends BaseModel implements BundleFieldContract
{
    use BundleField;
    
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
    protected function castSingleValue($value)
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
    protected function defaultValidationRule(): string
    {
        return 'boolean';
    }

}
