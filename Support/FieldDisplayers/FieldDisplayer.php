<?php

namespace Pingu\Field\Support;

use Illuminate\Support\Arr;
use Pingu\Entity\Entities\DisplayField;
use Pingu\Entity\Support\Entity;
use Pingu\Field\Contracts\FieldContract;
use Pingu\Field\Contracts\FieldDisplayerContract;
use Pingu\Field\Support\DisplayOptions;

abstract class FieldDisplayer implements FieldDisplayerContract
{
    /**
     * @var DisplayField
     */
    protected $displayField;

    /**
     * Constructor
     * 
     * @param array|null $options
     */
    public function __construct(DisplayField $displayField)
    {
        $this->displayField = $displayField;
    }

    /**
     * Resolve the value for a field
     * 
     * @param mixed $value
     * 
     * @return mixed
     */
    abstract public function getFieldValue($value);

    /**
     * Display field getter
     * 
     * @return DisplayField
     */
    public function getDisplayField(): DisplayField
    {
        return $this->displayField;
    }

    /**
     * Field getter
     * 
     * @return FieldContract
     */
    public function getField(): FieldContract
    {
        return $this->displayField->getField();
    }

    /**
     * @inheritDoc
     */
    public static function hasOptions(): bool
    {
        return false;
    }

    /**
     * @ingeritDoc
     */
    public function toArray()
    {
        return [
            'hasOptions' => $this::hasOptions(),
            'machineName' => $this::machineName(),
            'friendlyName' => $this::friendlyName()
        ];
    }

    public function getViewData(Entity $entity): array
    {
        return [
            'values' => $this->getFieldValues($entity)
        ];
    }

    /**
     * Get the field values for an entity
     * 
     * @param Entity $entity
     * 
     * @return array
     */
    public function getFieldValues(Entity $entity): array
    {
        $out = [];
        foreach (Arr::wrap($entity->{$this->displayField->field}) as $value) {
            $out[] = $this->getFieldValue($value);
        }
        return $out;
    }
}