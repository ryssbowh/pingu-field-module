<?php

namespace Pingu\Field\Support\FieldRepository;

use Illuminate\Support\Collection;
use Pingu\Field\Contracts\FieldRepository;

/**
 * Defines a field repository for a model. Designed to list
 * fields belonging to a model
 */
abstract class BaseFieldRepository extends FieldRepository
{
    /**
     * Fields defined in this repository.
     * Must return an aray of BaseField
     * 
     * @return array
     */
    abstract protected function fields(): array;

    /**
     * Build fields
     * 
     * @return Collection
     */
    protected function buildFields(): Collection
    {
        $fields = collect();
        foreach ($this->fields() as $field) {
            $field->setModel($this->object);
            $fields->put($field->machineName(), $field);
        }
        return $fields;
    }
}