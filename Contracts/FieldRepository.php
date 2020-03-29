<?php

namespace Pingu\Field\Contracts;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Pingu\Core\Entities\BaseModel;
use Pingu\Field\Events\FieldsRetrieved;
use Pingu\Field\Exceptions\FieldsException;

abstract class FieldRepository
{
    /**
     * @var string
     */
    protected $fieldsCacheKey = 'fields';

    /**
     * @var HasFieldsContract
     */
    protected $object;

    /**
     * @var Collection
     */
    protected $fields;

    /**
     * Constructor, will build the fields and saves them in Cache
     * 
     * @param HasFieldsContract $object
     */
    public function __construct(HasFieldsContract $object)
    {
        $this->object = $object;
    }

    /**
     * Build fields into a Collection
     * 
     * @return Collection
     */
    abstract protected function buildFields(): Collection;

    /**
     * Resolve field cache
     * 
     * @return Collection
     */
    protected function resolveFields(): Collection
    {
        if (is_null($this->fields)) {
            $_this = $this;
            $this->fields = \Field::getFieldsCache(
                $this->fieldsCacheKey, $this->object->identifier(), function () use ($_this) {
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
     * @return FieldContract|Collection
     */
    public function get($only = null)
    {
        if (!empty($only)) {
            $fields = $this->resolveFields()->only(Arr::wrap($only));
            if ($fields->isEmpty()) {
                throw FieldsException::undefined($only, $this->object);
            }
            if (is_array($only)) {
                return $fields;
            }
            return $fields->first();
        }
        return $this->resolveFields();
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
     * Alter fields collections
     * 
     * @param bool   $updating
     * @param Collection $fields
     */
    protected function alterFieldsForForm(Collection $fields, bool $updating)
    {
    }

    /**
     * Transform all (or some) fields into FormElements
     *
     * @param BaseModel    $model
     * @param bool         $updating
     * @param array|string $fields
     * 
     * @return array
     */
    public function toFormElements(BaseModel $model, bool $updating, $fields = null): array
    {
        if (!is_null($fields)) {
            $fields = $this->resolveFields()->only(Arr::wrap($fields));
        } else {
            $fields = $this->resolveFields();
        }
        
        $this->alterFieldsForForm($fields, $updating);

        return $fields->map(function ($field) use ($model) {
            $value = $field->formValue($model);
            return $field->toFormElement($value);
        })->all();
    }
    
}