<?php

namespace Pingu\Field\Entities;

use Illuminate\Database\Eloquent\Builder;
use Pingu\Core\Entities\BaseModel;
use Pingu\Entity\Support\Entity;
use Pingu\Field\Displayers\FakeDisplayer;
use Pingu\Forms\Support\Field;
use Pingu\Forms\Support\Fields\Checkbox;

class FieldBoolean extends BaseBundleField
{
    protected static $availableWidgets = [Checkbox::class];

    protected static $availableFilterWidgets = [Checkbox::class];

    protected static $displayers = [FakeDisplayer::class];
    
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
        return (bool)$this->default;
    }

    /**
     * @inheritDoc
     */
    public function uncastSingleValue($value)
    {
        return (bool)$value;
    }

    /**
     * @inheritDoc
     */
    public function castSingleValueFromDb($value)
    {
        return (bool)$value;
    }

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
    public function toSingleDbValue($value)
    {
        return (int)$value;
    }
    
    /**
     * @inheritDoc
     */
    public function formFieldOptions(int $index = 0): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function defaultValidationRule(): string
    {
        return 'boolean';
    }

    /**
     * @inheritDoc
     */
    public function singleFilterQueryModifier(Builder $query, $value, BaseModel $model)
    {
        $query->where('value', '=', (int)$value);
    }
}
