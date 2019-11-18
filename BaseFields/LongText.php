<?php

namespace Pingu\Field\BaseFields;

use Illuminate\Database\Query\Builder;
use Pingu\Field\Support\BaseField;
use Pingu\Forms\Support\Fields\Textarea;

class LongText extends BaseField
{
    /**
     * @inheritDoc
     */
    protected function defaultFormFieldClass(): string
    {
        return Textarea::class;
    }

    /**
     * @inheritDoc
     */
    public function filterQueryModifier(Builder $query, $value)
    {
        if ($value) {
            $query->where($this->machineName, 'like', '%'.$value.'%');
        }
    }

    /**
     * @inheritDoc
     */
    public function defaultValidationRules(): array
    {
        return [$this->machineName => 'string'];
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