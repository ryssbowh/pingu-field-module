<?php

namespace Pingu\Field\Entities;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Pingu\Core\Entities\BaseModel;
use Pingu\Entity\Entities\Entity;
use Pingu\Forms\Support\Field;
use Pingu\Forms\Support\Fields\Datetime;

class FieldDatetime extends BaseBundleField
{
    protected static $availableWidgets = [Datetime::class];
    
    protected static $availableFilterWidgets = [Datetime::class];

    protected $fillable = ['setToCurrent', 'required', 'format'];

    protected $casts = [
        'setToCurrent' => 'boolean',
        'required' => 'boolean'
    ];

    protected $dbFormat = 'Y-m-d H:i:s';

    /**
     * @inheritDoc
     */
    public function defaultValue()
    {
        if ($this->setToCurrent) {
            return Carbon::now();
        }
    }

    public function getFormat()
    {
        if (!$this->format) {
            return $this->dbFormat;
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
    public function uncastSingleValue($value)
    {
        if ($value instanceof Carbon) {
            return $value->format($this->getFormat());
        }
        return $value;
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
    public function toSingleDbValue($value)
    {
        if ($value) {
            return Carbon::createFromFormat($this->getFormat(), $value)->format($this->dbFormat);
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

    protected function tryCastingIntoFormat($value, $format)
    {
        try {
            return Carbon::createFromFormat($format, $value);
        } catch (InvalidArgumentException $e) {
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function formFieldOptions(int $index = 0): array
    {
        return [
            'format' => $this->getFormat(),
            'required' => $this->required
        ];
    }

    /**
     * @inheritDoc
     */
    public function defaultValidationRule(): string
    {
        return ($this->required ? 'required|' : '') . 'date_format:'.$this->getFormat();
    }

    /**
     * @inheritDoc
     */
    public function singleFilterQueryModifier(Builder $query, $value, BaseModel $model)
    {
        $query->where('value', '=', $value);
    }

}
