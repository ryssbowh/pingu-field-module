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
     * ast a value to a form readable format
     * 
     * @param mixed $value
     * 
     * @return mixed
     */
    public function castToSingleFormValue($value);

    /**
     * Cast a value coming from database
     * 
     * @param mixed $value
     * 
     * @return mixed
     */
    public function castValueFromDb($value);

    /**
     * Cast a single value coming from database
     * 
     * @param mixed $value
     * 
     * @return mixed
     */
    public function castSingleValueFromDb($value);

    /**
     * Cast a value to a database valid format
     * 
     * @param mixed $value
     * 
     * @return mixed
     */
    public function castValueToDb($value);

    /**
     * Cast a single value to a database valid format
     * 
     * @param mixed $value
     * 
     * @return mixed
     */
    public function castSingleValueToDb($value);

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