<?php

namespace Pingu\Field\BaseFields;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Pingu\Core\Entities\BaseModel;
use Pingu\Field\Exceptions\FieldsException;
use Pingu\Field\Support\BaseField;
use Pingu\Forms\Exceptions\FormFieldException;
use Pingu\Forms\Support\Fields\Checkboxes;
use Pingu\Forms\Support\Fields\Select;

class Model extends BaseField
{
    protected $requiredOptions = ['textField'];

    protected static $availableWidgets = [
        Select::class,
        Checkboxes::class
    ];

    protected static $availableFilterWidgets = [
        Select::class,
        Checkboxes::class
    ];

    /**
     * @inheritDoc
     */
    protected function init(array $options)
    {   
        if (!isset($options['items']) and !isset($options['model'])) {
            throw FieldsException::missingOption($this->name, 'items or model');
        }
        parent::init($options);
    }

    /**
     * @inheritDoc
     */
    protected function buildItems(bool $includeNoValue, $noValueLabel = 'Select')
    {
        $textField = Arr::wrap($this->option('textField'));
        $values = [];
        if ($includeNoValue) {
            $values[''] = $noValueLabel;
        }
        $items = $this->option('items') ?? $this->option('model')::all();
        foreach ($items as $model) {
            $values[''.$model->id] = implode($this->option('separator'), $model->only($textField));
        }
        return $values;
    }

    /**
     * @inheritDoc
     */
    public function filterQueryModifier(Builder $query, $value, BaseModel $model)
    {
        if (!$value) { return;
        }
        $query->where($this->machineName.'_id', '=', $value);
    }

    /**
     * @inheritDoc
     */
    public function definesRelation()
    {
        return 'single';
    }

    /**
     * @inheritDoc
     */
    protected function defaultOptions(): array
    {
        return array_merge(parent::defaultOptions(), [
            'multiple' => false,
            'required' => false,
            'noValueLabel' => theme_config('forms.noValueLabel', 'Select'),
            'separator' => ' - '
        ]);
    }

    /**
     * @inheritDoc
     */
    public function castToFormValue($value)
    {
        if ($value) {
            return (string)$value->getKey();
        }
    }

    /**
     * @inheritDoc
     */
    public function castValue($value)
    {
        if ($value) {
            $modelClass = $this->option('model');
            return $modelClass::find($value);   
        }
    }

    /**
     * @inheritDoc
     */
    public function defaultValidationRules(): array
    {
        return [$this->machineName => 'exists:'.$this->option('model')::tableName().',id'];
    }

    /**
     * @inheritDoc
     */
    public function formFieldOptions(int $index = 0): array
    {
        $options = $this->options->toArray();
        $options['items'] = $this->buildItems(!$options['required'], $options['noValueLabel']);
        return $options;
    }

    /**
     * @inheritDoc
     */
    public function formFilterFieldOptions(): array
    {
        $options = parent::formFilterFieldOptions();
        $options['items'] = $this->buildItems(false);
        $options['multiple'] = true;
        return $options;
    }
}