<?php

namespace Pingu\Field\Entities;

use Carbon\Carbon;
use Pingu\Forms\Support\Field;
use Pingu\Forms\Support\Fields\Entity;

class FieldEntity extends BaseBundleField
{
    protected static $availableWidgets = [Entity::class];

    protected $fillable = ['entity', 'required'];

    /**
     * @inheritDoc
     */
    public function defaultValue()
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public static function friendlyName(): string 
    {
        return 'Entity';
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
        return (string)$value->getKey();
    }

    /**
     * @inheritDoc
     */
    public function castSingleValue($value)
    {
        return $this->entity::find($value);
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
        return new Entity(
            $this->machineName(),
            [
                'label' => $this->name(),
                'showLabel' => false,
                'required' => $this->required, 
                'entity' => $this->entity
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function defaultValidationRule(): string
    {
        return ($this->required ? 'required|' : '');
    }

}
