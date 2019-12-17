<?php

namespace Pingu\Field\Entities;

use Carbon\Carbon;
use Pingu\Forms\Support\Field;
use Pingu\Forms\Support\Fields\Datetime;

class FieldDatetime extends BaseBundleField
{
    protected static $availableWidgets = [Datetime::class];

    protected $fillable = ['setToCurrent', 'required'];

    protected $casts = [
        'setToCurrent' => 'boolean',
        'required' => 'boolean'
    ];

    protected $format = "Y-m-d H:i:s";

    /**
     * @inheritDoc
     */
    public function defaultValue(bool $casted = false)
    {
        if ($this->setToCurrent) {
            return Carbon::now()->format($this->format);
        }
    }

    /**
     * @inheritDoc
     */
    public static function friendlyName(): string 
    {
        return 'Date and time';
    }

    /**
     * @inheritDoc
     */
    public function castSingleValue($value)
    {
        if ($value) {
            return Carbon::createFromFormat($this->format, $value);
        }
    }

    /**
     * @inheritDoc
     */
    public function singleFormValue($value)
    {
        return $value->format($this->format);
    }

    /**
     * @inheritDoc
     */
    public function toSingleFormField($value): Field
    {
        return new Datetime(
            $this->machineName(),
            [
                'label' => $this->name(),
                'showLabel' => false,
                'default' => $value ?? $this->default,
                'format' => $this->format,
                'required' => $this->required
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function defaultValidationRule(): string
    {
        return $this->required ? 'required' : '';
    }

}
