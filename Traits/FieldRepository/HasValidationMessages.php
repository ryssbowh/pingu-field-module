<?php

namespace Pingu\Field\Traits\FieldRepository;

use Illuminate\Support\Collection;

trait HasValidationMessages
{   
    /**
     * @var Collection
     */
    protected $messages;

    /**
     * Messages defined by this class
     * 
     * @return array
     */
    abstract protected function messages(): array;

    /**
     * Get messages from cache or build them
     * 
     * @return Collection
     */
    protected function resolveMessages(): Collection
    {
        if (is_null($this->messages)) {
            if (config('field.useCache', true)) {
                $key = config('field.cache-keys.fields').'.'.object_to_class($this->object).'.messages';
                $_this = $this;
                $this->messages = \ArrayCache::rememberForever(
                    $key, function () use ($_this) {
                        return $_this->buildMessages();
                    }
                );
            } else {
                $this->messages = $this->buildMessages();
            }
        }
        return $this->messages;
    }

    /**
     * Builds validation messages
     * 
     * @return array
     */
    protected function buildMessages(): Collection
    {
        return collect(array_merge($this->defaultFieldsMessages(), $this->messages()));
    }

    /**
     * Builds the default messages for all fields
     * 
     * @return array
     */
    protected function defaultFieldsMessages(): array
    {
        $out = [];
        foreach ($this->object->fieldRepository()->all() as $field) {
            $messages = $field->defaultValidationMessages();
            $out = array_merge($out, $messages);
        }
        return $out;
    }
}