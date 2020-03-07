<?php

namespace Pingu\Field\BaseFields;

use Pingu\Field\Support\BaseField;
use Pingu\Forms\Support\Fields\Checkboxes;
use Pingu\Forms\Support\Fields\Select;

class ManyModel extends Model
{
    protected static $availableWidgets = [
        Checkboxes::class,
        Select::class
    ];

    /**
     * @inheritDoc
     */
    public function filterQueryModifier(Builder $query, $value)
    {
        if(!$value) { return;
        }
        $name = $this->machineName;
        $model = $query->getModel()->buildFieldDefinitions()[$name]->option('model');
        $model = new $model;
        $query->whereHas(
            $name, function ($query) use ($model, $value) {
                $query->where(str_singular($model->getTable()).'_id', '=', $value);
            }
        );
    }

    /**
     * @inheritDoc
     */
    public function definesRelation()
    {
        return 'multiple';
    }

    /**
     * @inheritDoc
     */
    protected function defaultOptions(): array
    {
        return array_merge(parent::defaultOptions(), [
            'multiple' => true
        ]);
    }

    /**
     * @inheritDoc
     */
    public function defaultValidationRules(): array
    {
        return [$this->machineName.'.*' => 'exists:'.$this->option('model')::tableName().',id'];
    }

    /**
     * @inheritDoc
     */
    public function formValue($value)
    {
        if (!$value) {
            return [];
        }
        return $value->map(
            function ($model) {
                return (string)$model->getKey();
            }
        )->toArray();
    }
}