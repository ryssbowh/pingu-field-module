<?php

namespace Pingu\Field\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Pingu\Core\Entities\BaseModel;
use Pingu\Entity\Contracts\BundleContract;
use Pingu\Field\Contracts\FieldContract;
use Pingu\Field\Contracts\HasFieldsContract;

interface BundleFieldContract extends 
    FieldContract,
    HasFieldsContract
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
    public function uncastValue($value);

    /**
     * Transform a value into a db saveable value
     * 
     * @param  mixed $value
     * @return mixed
     */
    public function toSingleDbValue($value);

    /**
     * Cast a single value to a base type (int, string, bool, array)
     * 
     * @param mixed $value
     * 
     * @return mixed
     */
    public function uncastSingleValue($value);

    /**
     * Default validation rules
     * 
     * @return string
     */
    public function defaultValidationRule(): string;

    /**
     * Query filter modifier for a single value
     * 
     * @param Builder $query
     * @param mixed  $value
     * @param BaseModel  $model
     */
    public function singleFilterQueryModifier(Builder $query, $value, BaseModel $model);
}