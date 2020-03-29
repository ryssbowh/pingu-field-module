<?php

namespace Pingu\Field\Contracts;

use Illuminate\Http\Request;
use Illuminate\Validation\Validator;
use Illuminate\Validation\ValidatorContract;
use Pingu\Field\Contracts\FieldContract;
use Pingu\Field\Events\FieldsValidationMessagesRetrieved;
use Pingu\Field\Events\FieldsValidationRulesRetrieved;
use Pingu\Field\Events\FieldsValidatorBuilt;
use Pingu\Field\Exceptions\FieldsException;
use Pingu\Forms\Support\Field;
use Pingu\Media\Contracts\UploadsMedias;

abstract class FieldsValidator
{
    public $object;

    protected $rulesCacheKey = 'rules';
    protected $messagesCacheKey = 'messages';

    public function __construct(HasFieldsContract $object)
    {
        $this->object = $object;
    }

    /**
     * Build messages for this validator
     * 
     * @return array
     */
    abstract protected function buildMessages(): array;

    /**
     * Build rules for this validator
     * 
     * @return array
     */
    abstract protected function buildRules(bool $updating): array;

    /**
     * Validation messages
     * 
     * @see    https://laravel.com/docs/5.7/validation
     * @return array
     */
    public function getMessages(): array
    {
        $_this = $this;
        return \Field::getFieldsCache(
            $this->messagesCacheKey, $this->object, function () use ($_this) {
                $messages = $_this->buildMessages();
                event(new FieldsValidationMessagesRetrieved($messages, $_this->object));
                return $messages;
            }
        );
    }

    /**
     * Validation rules for this object, throws an event so
     * that other modules can add/remove rules, and stores them in cache.
     * The default type rules will be added for each field
     * 
     * @param array $fields
     * 
     * @see    https://laravel.com/docs/5.7/validation
     * @return array
     */
    public function getRules(bool $updating): array
    {
        $_this = $this;
        $key = $this->rulesCacheKey . ($updating ? '-updating' : '-creating');
        $rules = \Field::getFieldsCache(
            $this->rulesCacheKey, $this->object, function () use ($_this, $updating) {
                $rules = $_this->buildRules($updating);
                event(new FieldsValidationRulesRetrieved($rules, $_this->object, $updating));
                return $rules;
            }
        );
        return $rules;
    }

    /**
     * Validates a request and return validated data
     * 
     * @param Request $request
     * @param ?bool   $editing
     * 
     * @return array
     */
    public function validateRequest(Request $request, ?bool $updating = null): array
    {
        if (is_null($updating)) {
            $methods = $request->route()->methods();
            $updating = (in_array('PUT', $methods) or in_array('PATCH', $methods));
        }

        if ($updating) {
            return $this->validateUpdateRequest($request);
        }

        return $this->validateStoreRequest($request);
    }

    /**
     * Validates a request and return validated data
     * 
     * @param Request $request
     * @param bool    $cast
     * 
     * @return array
     */
    public function validateStoreRequest(Request $request, bool $cast = true): array
    {
        return $this->validateValues($request->all(), false, $cast);
    }

    /**
     * Validates a request and return validated data
     * 
     * @param Request $request
     * @param bool    $cast
     * 
     * @return array
     */
    public function validateUpdateRequest(Request $request, bool $cast = true): array
    {
        return $this->validateValues($request->all(), true, $cast);
    }

    /**
     * Validates an array of values and return validated data
     * 
     * @param array $values
     * @param bool  $updating
     * @param bool  $cast
     * 
     * @return array
     */
    public function validateValues(array $values, bool $updating = false, bool $cast = true): array
    {
        $validator = $this->makeValidator($values, $updating);
        $validator->validate();
        $validated = $validator->validated();
        $validated = $this->uploadMedias($validated);
        if ($cast) {
            return $this->castValues($validated);
        }
        return $validated;
    }

    /**
     * Makes a validator.
     * 
     * @param array $values
     * @param array $fields
     * 
     * @return Validator
     */
    public function makeValidator(array $values, bool $updating): Validator
    {   
        //remove non fillable fields
        $values = $this->removeNonFillableValues($values);
        // $values = $this->removeNonFormableValues($values);
        $rules = $this->getRules($updating);

        //if updating a object, making sure a validation rule exist for all posted fields
        //if creating a object, making sure a validation rule exist for all defined fields
        if ($updating) {
            $fieldsToCheck = array_keys($values);
        } else {
            $fieldsToCheck = $this->object->fields()->allNames();
        }

        $this->ensureRulesExist($fieldsToCheck, $rules);

        if ($updating) {
            $rules = $this->prependSometimesRule($rules);
        }

        $validator = \Validator::make($values, $rules, $this->getMessages());
        $this->modifyValidator($validator, $values, $updating);
        event(new FieldsValidatorBuilt($validator, $this->object));

        return $validator;
    }

    protected function prependSometimesRule(array $rules)
    {
        return array_map(function ($rule) {
            return $rule ? 'sometimes|'.$rule : '';
        }, $rules);
    }

    // protected function intersectRuleKeys($rules, $keys)
    // {
    //     $out = [];
    //     foreach ($rules as $name => $rule) {
    //         $elems = explode('.', $name);
    //         if (in_array($elems[0], $keys)) {
    //             $out[$name] = $rule;
    //         }
    //     }
    //     return $out;
    // }

    /**
     * Go through all the fields and make them upload the files
     * 
     * @param  array $values
     * @return array
     */
    public function uploadMedias(array $values): array
    {
        foreach ($values as $fieldName => $value) {
            $field = $this->object->fields()->get($fieldName);
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
     * Ensure that a rule is defined for every field
     * 
     * @param array $fields
     * 
     * @throws FieldsException
     */
    protected function ensureRulesExist(array $fields, array $rules)
    {
        foreach ($fields as $field) {
            if (!isset($rules[$field]) and !isset($rules[$field.'.*'])) {
                throw FieldsException::missingRule($field, $this->object);
            }
        }
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
            $field = $this->object->fields()->get($name);
            $out[$name] = $field->castValue($value);
        }
        return $out;
    }

    /**
     * Hook to add rules to the validator
     * 
     * @param Validator $validator
     * @param array     $values
     * @param bool      $updating
     */
    protected function modifyValidator(Validator $validator, array $values, bool $updating)
    {
    }

    /**
     * Merges two sets of rules. if a rule exists in $rule
     * it will override the same rule in $default.
     * All rules in $default that do not exist in $rule will be added to the result
     * 
     * @param string $rule    example : 'required|min:8'
     * @param string $default example : 'string|min:6'
     * 
     * @return string example 'string|required|min:8'
     */
    protected function mergeRules(string $rule, string $default)
    {
        if (!$rule) {
            return $default;
        }
        if (!$default) {
            return $rule;
        }
        $rulesByName  = [];

        foreach (explode('|', $rule) as $rule) {
            $elems = explode(':', $rule);
            $rulesByName[$elems[0]] = $elems[1] ?? '';
        }
        foreach (explode('|', $default) as $default) {
            $elems = explode(':', $default);
            $rulesByName[$elems[0]] = $elems[1] ?? '';
        }

        $rules = [];
        foreach ($rulesByName as $name => $rule) {
            if ($rule) {
                $name .= ':'.$rule;
            }
            $rules[] = $name;
        }
        return implode('|', $rules);
    }

    /**
     * Builds the rules for an object's fields
     * 
     * @return array
     */
    protected function defaultFieldsRules(): array
    {
        $out = [];
        foreach ($this->object->fields()->get() as $field) {
            $rules = $field->defaultValidationRules();
            $out = array_merge($out, $rules);
        }
        return $out;
    }

    /**
     * Builds the rules for an object's fields 
     * 
     * @return array
     */
    protected function defaultFieldsMessages(): array
    {
        $out = [];
        foreach ($this->object->fields()->get() as $field) {
            $messages = $field->defaultValidationMessages();
            $out = array_merge($out, $messages);
        }
        return $out;
    }
}