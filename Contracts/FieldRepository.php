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
     * @var HasFields
     */
    protected $object;

    /**
     * @var Collection
     */
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
                $this->fieldsCacheKey, $this->getObjectCacheTarget(), function () use ($_this) {
                    $fields = $_this->buildFields();
                    event(new FieldsRetrieved($fields, $_this->object));
                    return $fields;
                }
            );
        }
        return $this->fields;
    }

    protected function getObjectCacheTarget()
    {
        return $this->object;
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
            return $field->toFormElement($model);
        })->all();
    }
    
}