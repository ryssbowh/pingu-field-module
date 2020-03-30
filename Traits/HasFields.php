<?php

namespace Pingu\Field\Traits;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Pingu\Field\Contracts\FieldContract;
use Pingu\Field\Contracts\FieldRepository;
use Pingu\Field\Contracts\FieldsValidator;
use Pingu\Field\Entities\BundleField;
use Pingu\Field\Support\FieldLayout;

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
    abstract protected function getFieldRepository(): FieldRepository;

    /**
     * Gets the field validator for this model
     * 
     * @return FieldsValidator
     */
    abstract protected function getFieldsValidator(): FieldsValidator;

    /**
     * Gets the field repository for this model by loading it through the Field Facade
     * 
     * @return FieldRepository
     */
    public function fields(): FieldRepository
    {
        $_this = $this;
        return \Field::getFieldRepository(
            $this,
            function () use ($_this) {
                return $_this->getFieldRepository();
            }
        );
    }

    /**
     * Gets the field validator for this model by loading it through the Field Facade
     * 
     * @return FieldRepository
     */
    public function validator(): FieldsValidator
    {
        $_this = $this;
        return \Field::getFieldsValidator(
            $this,
            function () use ($_this) {
                return $_this->getFieldsValidator();
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
            $this->fields()->get()->all(),
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
}