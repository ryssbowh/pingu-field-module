<?php 

namespace Pingu\Field\Traits\FieldRepository;

use Illuminate\Support\Collection;
use Pingu\Field\Contracts\FieldContract;
use Pingu\Field\Exceptions\FieldsException;

trait HasFields
{
    /**
     * @var Collection
     */
    protected $fields;

    /**
     * Fields defined in this repository.
     * Must return an array of FieldContract
     * 
     * @return array
     */
    abstract protected function fields(): array;

    /**
     * @inheritDoc
     */
    public function all(): Collection
    {
        return $this->resolveFields();
    }

    /**
     * @inheritDoc
     */
    public function get(string $name): FieldContract
    {
        if (!$this->has($name)) {
            throw FieldsException::undefined($name, $this->object);
        }
        return $this->resolveFields()->get($name);
    }

    /**
     * @inheritDoc
     */
    public function names(): array
    {
        return $this->resolveFields()->keys()->all();
    }

    /**
     * Build fields
     * 
     * @return Collection
     */
    protected function buildFields(): Collection
    {
        $fields = collect();
        foreach ($this->fields() as $field) {
            $fields->put($field->machineName(), $field);
        }
        return $fields;
    }

    /**
     * Resolve field cache
     * 
     * @return Collection
     */
    protected function resolveFields(): Collection
    {
        if (is_null($this->fields)) {
            $_this = $this;
            if (config('field.useCache', true)) {
                $key = config('field.cache-keys.fields').'.'.object_to_class($this->object).'.fields';
                $this->fields = \ArrayCache::rememberForever(
                    $key, function () use ($_this) {
                        return $_this->buildFields();
                    }
                );
            } else {
                $this->fields = $this->buildFields();
            }
        }
        return $this->fields;
    }
}