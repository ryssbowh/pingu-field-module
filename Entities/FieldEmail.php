<?php

namespace Pingu\Field\Entities;

use Illuminate\Database\Eloquent\Builder;
use Pingu\Core\Entities\BaseModel;
use Pingu\Entity\Entities\Entity;
use Pingu\Forms\Support\Field;
use Pingu\Forms\Support\Fields\Email;
use Pingu\Forms\Support\Fields\TextInput;

class FieldEmail extends BaseBundleField
{
    protected static $availableWidgets = [Email::class];

    protected static $availableFilterWidgets = [TextInput::class];

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
    public function uncastSingleValue($value)
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
        return ($this->required ? 'required|' : '') . 'email';
    }

    /**
     * @inheritDoc
     */
    public function singleFilterQueryModifier(Builder $query, $value, BaseModel $model)
    {
        $query->where('value', '=', $value);
    }
}
