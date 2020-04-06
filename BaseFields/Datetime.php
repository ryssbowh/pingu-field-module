<?php

namespace Pingu\Field\BaseFields;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Pingu\Core\Entities\BaseModel;
use Pingu\Field\Displayers\DefaultDateDisplayer;
use Pingu\Field\Support\BaseField;
use Pingu\Forms\Support\Fields\Datetime as DatetimeField;

class Datetime extends BaseField
{
    protected $format = 'd/m/Y H:i:s';

    protected $requiredOptions = ['format'];

    protected static $availableWidgets = [DatetimeField::class];
    
    protected static $availableFilterWidgets = [DatetimeField::class];

    protected static $displayers = [DefaultDateDisplayer::class];

    public function getFormat()
    {
        return $this->option('format') ?? $this->format;
    }

    /**
     * @inheritDoc
     */
    public function filterQueryModifier(Builder $query, $value, BaseModel $model)
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
        return array_merge(parent::defaultOptions(), [
            'format' => $this->format
        ]);
    }

    /**
     * @inheritDoc
     */
    public function defaultValidationRules(): array
    {
        return [$this->machineName => 'date_format:'.$this->getFormat()];
    }

    /**
     * @inheritDoc
     */
    public function castToFormValue($value)
    {
        if ($value) {
            return $value->format($this->getFormat());
        }
    }

    /**
     * @inheritDoc
     */
    public function castValue($value)
    {
        if ($value) {
            return Carbon::fromFormat($this->getFormat());
        }
    }
}