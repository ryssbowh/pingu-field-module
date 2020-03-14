<?php

namespace Pingu\Field\Contracts; 

use Pingu\Forms\Support\Field;
use Pingu\Forms\Support\FormElement;

interface FieldContract extends HasWidgetsContracts, HasFilterWidgetsContracts
{
    /**
     * Name of the field
     * 
     * @return string
     */
    public function name(): string;

    /**
     * Machine name of the field
     * 
     * @return string
     */
    public function machineName(): string;

    /**
     * Does this field have a fixed cardinality
     * 
     * @return false|int
     */
    public function fixedCardinality();

    /**
     * Cardinality of this field
     * 
     * @return int
     */
    public function cardinality(): int;

    /**
     * Default validation rules, added to every instance of that field
     * 
     * @return array
     */
    public function defaultValidationRules(): array;

    /**
     * Default validation messages, added to every instance of that field
     * 
     * @return array
     */
    public function defaultValidationMessages(): array;

    /**
     * Does this field define a relation, returns false, 'single', or 'multiple'
     * This will be used to save the value of that field on a model
     * 
     * @return false|string
     */
    public function definesRelation();

    /**
     * Get this field transformed into a form element
     * 
     * @return FormElement
     */
    public function toFormElement(): FormElement;

    /**
     * Get this field transformed into a form element
     * 
     * @return FormElement
     */
    public function toFilterFormElement(): FormElement;

    /**
     * Default value for that field
     * 
     * @return mixed
     */
    public function defaultValue();

    /**
     * Transfom a value on a format usable in a form
     * 
     * @param mixed $value
     * 
     * @return mixed
     */
    public function castToFormValue($value);

    /**
     * Cast a raw value
     * 
     * @param mixed $value
     * 
     * @return mixed
     */
    public function castValue($value);

    /**
     * Options for a form field
     * 
     * @return array
     */
    public function formFieldOptions(): array;

}