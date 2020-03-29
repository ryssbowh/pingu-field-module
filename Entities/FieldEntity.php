<?php

namespace Pingu\Field\Entities;

use Carbon\Carbon;
use Pingu\Field\Traits\HandlesModel;
use Pingu\Forms\Support\Fields\Select;

class FieldEntity extends BaseBundleField
{
    use HandlesModel;

    protected static $availableWidgets = [Select::class];
    
    protected static $availableFilterWidgets = [Select::class];

    protected $fillable = ['entity', 'required'];

    /**
     * @inheritDoc
     */
    protected function getModel(): string
    {
        return $this->entity;
    }

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

}
