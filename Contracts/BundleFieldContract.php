<?php

namespace Pingu\Field\Contracts;

use Pingu\Core\Entities\BaseModel;
use Pingu\Entity\Contracts\BundleContract;
use Pingu\Forms\Support\Field;

interface BundleFieldContract extends FieldContract, HasFields
{
   
    /**
     * Unique name for that field
     *
     * @return string
     */
    public static function uniqueName(): string;

    /**
     * Cast a value from a form to a model usable value
     * 
     * @param mixed $value
     * 
     * @return mixed
     */
    public function castSingleValue($value);

    /**
     * Cast a value from a model into a form usable format
     * 
     * @param mixed $value
     * 
     * @return mixed
     */
    public function singleFormValue($value);

    /**
     * Turn a value of this field into a FormElement
     *
     * @param mixed $value
     * 
     * @return Field
     */
    public function toSingleFormField($value): Field;

    /**
     * Default validation rules
     * 
     * @return string
     */
    public function defaultValidationRule(): string;
}