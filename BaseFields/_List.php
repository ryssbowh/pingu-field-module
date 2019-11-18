<?php

namespace Pingu\Field\BaseFields;

use Illuminate\Database\Query\Builder;
use Pingu\Field\Support\BaseField;
use Pingu\Forms\Support\Fields\Select;

class _List extends BaseField
{
    protected $requiredOptions = ['items'];

    /**
     * @inheritDoc
     */
    protected function defaultFormFieldClass(): string
    {
        return Select::class;
    }

    /**
     * @inheritDoc
     */
    public function defaultValidationRules(): array
    {
        return [$this->machineName => 'in:'.implode(',', array_keys($this->option('items')))];
    }

    /**
     * @inheritDoc
     */
    public function castValue($value)
    {
        return $value;
    }

    /**
     * @inheritDoc
     */
    public function formValue($value)
    {
        return $value;
    }
}