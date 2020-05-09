<?php

namespace Pingu\Field\Support;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Pingu\Core\Contracts\RouteContexts\ValidatableContextContract;
use Pingu\Field\Contracts\HasFieldsContract;
use Pingu\Field\Exceptions\FieldsException;

class FieldValidator
{
    /**
     * @var BaseModel
     */
    protected $object;

    public function __construct(HasFieldsContract $object)
    {
        $this->object = $object;
    }

    /**
     * Makes an instance for a model
     * 
     * @param BaseModel $object
     * 
     * @return FieldValidator
     */
    public static function forModel(HasFieldsContract $object): FieldValidator
    {
        return new FieldValidator($object);
    }

    /**
     * Makes an instance for a model
     * 
     * @param BaseModel $object
     * 
     * @return FieldValidator
     */
    public static function forContext(HasFieldsContract $object, ValidatableContextContract $context): ContextValidator
    {
        return new ContextValidator($object, $context);
    }

    /**
     * Validation messages
     * 
     * @see    https://laravel.com/docs/5.8/validation
     * @return array
     */
    public function getMessages(): array
    {
        return $this->object->fieldRepository()->validationMessages()->all();
    }

    /**
     * Rules for validation
     * 
     * @see    https://laravel.com/docs/5.8/validation
     * @return array
     */
    public function getRules(): array
    {
        return $this->object->fieldRepository()->validationRules()->all();
    }

    /**
     * Fields to be validated
     * 
     * @return Collection
     */
    public function getFields(): Collection
    {
        return $this->object->fieldRepository()->all();
    }

    /**
     * Validates a request and return validated data
     * 
     * @param Request $request
     * @param $cast cast values after they're validated
     * 
     * @return array
     */
    public function validateRequest(Request $request, bool $cast = true): array
    {
        return $this->validateData($request->all(), $cast);
    }

    /**
     * Validate an array of data
     * 
     * @param array        $data
     * @param bool|boolean $cast
     * 
     * @return array
     */
    public function validateData(array $data): array
    {
        $validator = $this->makeValidator($data);
        $validator->validate();
        $validated = $validator->validated();
        return $this->afterValidated($validated);
    }

    /**
     * After validation treatment
     * 
     * @param array $validated
     * 
     * @return array
     */
    public function afterValidated(array $validated): array
    {
        $validated = $this->uploadMedias($validated);
        $validated = $this->castValues($validated);
        return $validated;
    }

    /**
     * Makes a laravel validator.
     * 
     * @param array $values
     * 
     * @return Validator
     */
    public function makeValidator(array $values): Validator
    {   
        //remove non fillable fields
        $values = $this->removeNonFillableValues($values);

        $rules = $this->getRules();
        // dump($rules);

        return \Validator::make($values, $rules, $this->getMessages());
    }

    /**
     * Go through all the fields and make them upload the files
     * 
     * @param  array $values
     * @return array
     */
    public function uploadMedias(array $values): array
    {
        foreach ($values as $fieldName => $value) {
            $field = $this->getFields()->get($fieldName);
            if ($field instanceof UploadsMedias) {
                $values[$fieldName] = $field->uploadMedias($value);
            }
        }
        return $values;
    }

    /**
     * Removes all the values that starts with underscore
     * 
     * @param  array $values
     * @return array
     */
    protected function removeNonFillableValues(array $values): array
    {
        return array_filter(
            $values, function ($key) {
                return substr($key, 0, 1) != '_';
            }, ARRAY_FILTER_USE_KEY
        );
    }

    /**
     * Go through all the fields and make them cast the values
     * 
     * @param  array $validated
     * @return array
     */
    public function castValues(array $validated)
    {
        $out = [];
        foreach ($validated as $name => $value) {
            $field = $this->getFields()->get($name);
            $out[$name] = $field->castValue($value);
        }
        return $out;
    }
}