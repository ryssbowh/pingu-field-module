<?php

namespace Pingu\Field\BaseFields;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;
use Pingu\Field\Support\BaseField;
use Pingu\Forms\Support\Fields\Checkboxes;
use Pingu\Forms\Support\Fields\Select;

class _List extends BaseField
{
    protected $requiredOptions = ['items'];

    protected static $availableWidgets = [
        Select::class,
        Checkboxes::class
    ];

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
    public function castToFormValue($value)
    {
        return $value ? $value : [];
    }

    /**
     * @inheritDoc
     */
    public function castValue($value)
    {
        if ($value) {
            return implode(',', Arr::wrap($value));
        }
        return '';
    }
}