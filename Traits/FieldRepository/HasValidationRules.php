<?php

namespace Pingu\Field\Traits\FieldRepository;

use Illuminate\Support\Collection;

trait HasValidationRules
{
    /**
     * @var Collection
     */
    protected $rules;

    /**
     * @inheritDoc
     */
    abstract protected function rules(): array;

    /**
     * Resolve rules, buidl them or retrieve them from cache
     * 
     * @return Collection
     */
    protected function resolveRules(): Collection
    {
        if (is_null($this->rules)) {
            $_this = $this;
            if (config('field.useCache', true)) {
                $key = config('field.cache-keys.fields').'.'.object_to_class($this->object).'.rules';
                $this->rules = \ArrayCache::rememberForever(
                    $key, function () use ($_this) {
                        return $_this->buildRules();
                    }
                );
            } else {
                $this->rules = $this->buildRules();
            }
        }
        return $this->rules;
    }

    /**
     * Build rules from object and each of its fields
     * 
     * @return Collection
     */
    protected function buildRules(): Collection
    {
        $rules = $this->rules();
        $default = $this->defaultFieldsRules();
        $out = [];
        $allRules = array_merge(array_keys($rules), array_keys($default));
        foreach ($allRules as $name) {
            $out[$name] = $this->mergeRules($rules[$name] ?? '', $default[$name] ?? '');
        }
        return collect($out);
    }

    /**
     * Builds the rules for an object's fields
     * 
     * @return array
     */
    protected function defaultFieldsRules(): array
    {
        $out = [];
        foreach ($this->object->fieldRepository()->all() as $field) {
            $rules = $field->defaultValidationRules();
            $out = array_merge($out, $rules);
        }
        return $out;
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
}