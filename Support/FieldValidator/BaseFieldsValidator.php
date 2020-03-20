<?php

namespace Pingu\Field\Support\FieldValidator;

use Pingu\Field\Contracts\FieldsValidator;

abstract class BaseFieldsValidator extends FieldsValidator
{
    /**
     * @inheritDoc
     */
    abstract protected function messages(): array;

    /**
     * @inheritDoc
     */
    abstract protected function rules(): array;

    /**
     * @inheritDoc
     */
    protected function buildMessages(): array
    {
        return array_merge($this->defaultFieldsMessages(), $this->messages());
    }
    
    /**
     * @inheritDoc
     */
    protected function buildRules(): array
    {
        $rules = $this->rules();
        $default = $this->defaultFieldsRules();
        $out = [];
        $allRules = array_merge(array_keys($rules), array_keys($default));
        foreach ($allRules as $name) {
            $out[$name] = $this->mergeRules($rules[$name] ?? '', $default[$name] ?? '');
        }
        return $out;
    }
}