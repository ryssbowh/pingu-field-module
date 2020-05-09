<?php

namespace Pingu\Field\Traits;

use Illuminate\Support\Collection;
use Pingu\Field\Contracts\FieldRepositoryContract;

trait HasFields
{
    /**
     * Fields that are not filterable
     * 
     * @var array
     */
    protected $notFilterable = [];

    /**
     * Gets the field repository for this model
     * 
     * @return FieldRepository
     */
    abstract protected function fieldRepositoryInstance(): FieldRepositoryContract;

    /**
     * Gets the field repository for this model by loading it through the Field Facade
     * 
     * @return FieldRepository
     */
    public function fieldRepository(): FieldRepositoryContract
    {
        $_this = $this;
        return \Field::getFieldRepository(
            $this->identifier(),
            function () use ($_this) {
                return $_this->fieldRepositoryInstance();
            }
        );
    }

    /**
     * Field names that can be filtered on
     * 
     * @return array
     */
    public function getFilterable(): array
    {
        $filterable = array_keys(array_filter(
            $this->fieldRepository()->all()->all(),
            function ($field) {
                return $field->filterable();
            }
        ));
        return array_diff($filterable, $this->notFilterable);
    }

    /**
     * Set the field names that can be filtered on
     * 
     * @param array $filterable
     */
    public function setFilterable(array $filterable)
    {
        $this->filterable = $filterable;
    }

    /**
     * @inheritDoc
     */
    public function getFields(): Collection
    {
        return $this->fieldRepository()->all();
    }
}