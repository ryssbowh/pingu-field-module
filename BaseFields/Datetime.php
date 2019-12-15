<?php

namespace Pingu\Field\BaseFields;

use Carbon\Carbon;
use Pingu\Field\Support\BaseField;
use Pingu\Forms\Support\Fields\Datetime as DatetimeField;

class Datetime extends BaseField
{
    protected $format = 'd/m/Y H:i:s';

    protected static $availableWidgets = [DatetimeField::class];

    /**
     * @inheritDoc
     */
    public function filterQueryModifier(Builder $query, $value)
    {
        if (!$value) {
            return;
        }
        $name = $this->machineName;
        if (isset($value['from']) and $value['from']) {
            $query->where($name, '>=', $value['from']);
        }
        if (isset($value['to']) and $value['to']) {
            $query->where($name, '<=', $value['to']);
        }
    }

    /**
     * @inheritDoc
     */
    protected function defaultOptions(): array
    {
        return [
            'format' => $this->format
        ];
    }

    /**
     * @inheritDoc
     */
    public function defaultValidationRules(): array
    {
        return [$this->machineName => 'date_format:'.$this->format];
    }

    /**
     * @inheritDoc
     */
    public function castValue($value)
    {
        return Carbon::fromFormat($this->format, $value);
    }

    /**
     * @inheritDoc
     */
    public function formValue($value)
    {
        return $value->format($this->format);
    }
}