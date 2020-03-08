<?php

namespace Pingu\Field\BaseFields;

use Illuminate\Database\Eloquent\Collection;
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
    public function castToFormValue($value)
    {
        return array_map(function ($item) {
            return (string)$item->getKey();
        }, $value->all());
    }

    /**
     * @inheritDoc
     */
    public function castValue($value)
    {
        $model = $this->option('model');
        return new Collection(array_map(function ($id) use ($model) {
            return $model::find($id);
        }, $value));
    }
}