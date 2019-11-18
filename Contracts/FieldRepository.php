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
        $_this = $this;
        $this->fields = \Field::getCache(
            $this->fieldsCacheKey, $object, function () use ($_this) {
                $fields = $_this->buildFields();
                event(new FieldsRetrieved($fields, $_this->object));
                return $fields;
            }
        );
    }

    /**
     * Build fields into a Collection
     * 
     * @return Collection
     */
    abstract protected function buildFields(): Collection;

    /**
     * Returns a collection of object's fields (or a single field).
     * fields will be kept in cache forever.
     *
     * @param array|string $fields
     * 
     * @return Field|Collection
     */
    public function get($fields = null)
    {
        if (!is_null($fields)) {
            if (is_array($fields)) {
                return $this->fields->only($fields);
            } else {
                if (!$this->fields->has($fields)) {
                    throw FieldsException::undefined($fields, $this->object);
                }
                return $this->fields->get($fields);
            }
        }
        return $this->fields;
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
        return $this->get()->has($name);
    }

    /**
     * Returns the names of all fields
     * 
     * @return array
     */
    public function allNames(): array
    {
        return $this->get()->keys()->all();
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
            $fields = $this->get()->only(Arr::wrap($fields));
        } else {
            $fields = $this->get();
        }

        $out = [];
        foreach ($fields as $field) {
            $out[] = $field->toFormElement();
        }
        return $out;
    }
    
}