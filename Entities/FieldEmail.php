<?php

namespace Pingu\Field\Entities;

use Pingu\Core\Entities\BaseModel;
use Pingu\Forms\Support\Field;
use Pingu\Forms\Support\Fields\Email;

class FieldEmail extends BaseBundleField
{
    protected static $availableWidgets = [Email::class];

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
        return new Email(
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
        return ($this->required ? 'required|' : '') . 'email';
    }
}
