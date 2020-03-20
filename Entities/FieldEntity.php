<?php

namespace Pingu\Field\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Pingu\Entity\Entities\Entity as EntityModel;
use Pingu\Forms\Support\Field;
use Pingu\Forms\Support\Fields\Entity;
use Pingu\Forms\Support\Fields\Select;

class FieldEntity extends BaseBundleField
{
    protected static $availableWidgets = [Select::class];
    
    protected static $availableFilterWidgets = [Select::class];

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
        return $value->getKey();
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
        return $this->getAttribute('entity')::find($value);
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
    public function formFieldOptions(int $index = 0): array
    {
        return [
            'items' => (new $this->entity)->pluck('name', 'id')->all(),
            'required' => $this->required, 
            'entity' => $this->entity
        ];
    }

    /**
     * @inheritDoc
     */
    public function defaultValidationRule(): string
    {
        return ($this->required ? 'required|' : '');
    }

    /**
     * @inheritDoc
     */
    public function singleFilterQueryModifier(Builder $query, $value, EntityModel $entity)
    {
        $query->where('value', '=', $value);
    }

}
