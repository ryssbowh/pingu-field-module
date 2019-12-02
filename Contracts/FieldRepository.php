<?php

namespace Pingu\Field\Contracts;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Pingu\Core\Entities\BaseModel;
use Pingu\Field\Events\FieldsRetrieved;
use Pingu\Field\Exceptions\FieldsException;

abstract class FieldRepository
{
    protected $fieldsCacheKey = 'fields';
    protected $object;
    protected $fields;

    /**
     * Constructor, will build the fields and saves them in Cache
     * 
     * @param HasFields $object
     */
    public function __construct(HasFields $object)
    {
        $this->object = $object;
    }

    /**
     * Build fields into a Collection
     * 
     * @return Collection
     */
    abstract protected function buildFields(): Collection;

    protected function resolveFields()
    {
        if (is_null($this->fields)) {
            $_this = $this;
            $this->fields = \Field::getFieldsCache(
                $this->fieldsCacheKey, $this->object, function () use ($_this) {
                    $fields = $_this->buildFields();
                    event(new FieldsRetrieved($fields, $_this->object));
                    return $fields;
                }
            );
        }
        return $this->fields;
    }

    /**
     * Returns a collection of object's fields (or a single field).
     * fields will be kept in cache forever.
     *
     * @param array|string $only
     * 
     * @return Field|Collection
     */
    public function get($only = null)
    {
        $fields = $this->resolveFields();
        if (!is_null($only)) {
            if (is_array($only)) {
                return $fields->only($only);
            } else {
                if (!$fields->has($only)) {
                    throw FieldsException::undefined($only, $this->object);
                }
                return $fields->get($only);
            }
        }
        return $fields;
    }

    /**
     * Does this repository has a field called $name
     * 
     * @param string $name
     *
     * @return boolean
     */
    public function has(string $name): bool
    {
        return $this->resolveFields()->has($name);
    }

    /**
     * Returns the names of all fields
     * 
     * @return array
     */
    public function allNames(): array
    {
        return $this->resolveFields()->keys()->all();
    }

    /**
     * Clear the fields cache
     */
    public function clearCache()
    {
        \Field::clearCache($this->object);
    }

    /**
     * Transform all (or some) fields into FormElements
     * 
     * @param  array|string $fields
     * @return array
     */
    public function toFormElements($fields = null): array
    {
        if (!is_null($fields)) {
            $fields = $this->resolveFields()->only(Arr::wrap($fields));
        } else {
            $fields = $this->resolveFields();
        }

        $out = [];
        foreach ($fields as $field) {
            $out[] = $field->toFormElement();
        }
        return $out;
    }
    
}