<?php

namespace Pingu\Field\Entities;

use Carbon\Carbon;
use Pingu\Forms\Support\Field;
use Pingu\Forms\Support\Fields\Datetime;

class FieldDatetime extends BaseBundleField
{
    protected static $availableWidgets = [Datetime::class];

    protected $fillable = ['setToCurrent', 'required', 'format'];

    protected $casts = [
        'setToCurrent' => 'boolean',
        'required' => 'boolean'
    ];

    protected $defaultFormat = "Y-m-d H:i:s";
    protected $dbFormat = 'Y-m-d H:i:s';

    /**
     * @inheritDoc
     */
    public function defaultValue()
    {
        if ($this->setToCurrent) {
            return Carbon::now()->format($this->getFormat());
        }
    }

    public function getFormat()
    {
        if (!$this->format) {
            return $this->defaultFormat;
        }
        return $this->format;
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
    public function castSingleValueToDb($value)
    {
        if ($value) {
            return $value->format($this->dbFormat);
        }
    }

    /**
     * @inheritDoc
     */
    public function castToSingleFormValue($value)
    {
        if ($value) {
            return $value->format($this->getFormat());
        }
    }

    /**
     * @inheritDoc
     */
    public function castSingleValue($value)
    {
        if ($value) {
            return Carbon::createFromFormat($this->getFormat(), $value);
        }
    }

    /**
     * @inheritDoc
     */
    public function castSingleValueFromDb($value)
    {
        if ($value) {
            return Carbon::createFromFormat($this->dbFormat, $value)->format($this->getFormat());
        }
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
                'default' => $value ?? $this->defaultValue(),
                'format' => $this->getFormat(),
                'required' => $this->required
            ]
        );
    }

    /**
     * @inheritDoc
     */
    public function defaultValidationRule(): string
    {
        return ($this->required ? 'required|' : '') . 'date_format:'.$this->getFormat();
    }

}
