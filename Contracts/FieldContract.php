<?php

namespace Pingu\Field\Contracts; 

use Pingu\Forms\Support\Field;
use Pingu\Forms\Support\FormElement;

interface FieldContract
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
     * Default value for that field
     * 
     * @return mixed
     */
    public function defaultValue();

    /**
     * Cast this field value, from a form value to a value usable on a model
     * 
     * @param mixed $value
     * 
     * @return mixed
     */
    public function castValue($value);

    /**
     * Transfom a value on a format usable in a form
     * 
     * @param mixed $value
     * 
     * @return mixed
     */
    public function formValue($value);

    /**
     * Get this field value
     * 
     * @param bool $casted
     * 
     * @return mixed
     */
    public function value(bool $casted = true);

    /**
     * List of form elements that are available to render this field
     * 
     * @return array
     */
    public static function availableWidgets(): array;

    /**
     * Default form element to render this field
     * 
     * @return string
     */
    public static function defaultWidget(): string;

    /**
     * Set the widget to render this field
     *
     * @param string $widget
     */
    public function setWidget(string $widget);

    /**
     * Widget getter, returns form field class
     * 
     * @return string
     */
    public function widget(): string;

}