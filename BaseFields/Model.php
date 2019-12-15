<?php

namespace Pingu\Field\BaseFields;

use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
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

    public function __construct(string $machineName, array $options = [], ?string $name = null, ?string $formFieldClass = null)
    {
        if (isset($options['items']) and isset($options['items'][0])) {
            $options['model'] = get_class($options['items'][0]);
        } elseif (isset($options['model'])) {
            $options['items'] = $options['model']::all();
        } else {
            throw FieldsException::missingOption($machineName, 'items or model');
        }
        
        parent::__construct($machineName, $options, $name, $formFieldClass);
    }

    /**
     * @inheritDoc
     */
    protected function init()
    {
        $this->option('items', $this->buildItems());
    }

    /**
     * @inheritDoc
     */
    protected function buildItems()
    {
        $textField = Arr::wrap($this->option('textField'));
        $values = [];
        if (!$this->option('required')) {
            $values[''] = $this->option('noValueLabel');
        }
        foreach ($this->option('items') as $model) {
            $values[''.$model->id] = implode($this->option('separator'), $model->only($textField));
        }
        return $values;
    }

    /**
     * @inheritDoc
     */
    public function filterQueryModifier(Builder $query, $value)
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
        return [
            'multiple' => false,
            'required' => false,
            'noValueLabel' => theme_config('forms.noValueLabel', 'Select'),
            'separator' => ' - '
        ];
    }

    /**
     * @inheritDoc
     */
    public function formValue($value)
    {
        if ($value) {
            return (string)$value->getKey();
        }
        return null;
    }

    /**
     * @inheritDoc
     */
    public function castValue($value)
    {
        if (!$value) {
            return null;
        }
        
        $modelClass = $this->option('model');
        return $modelClass::findOrFail($value);
    }

    /**
     * @inheritDoc
     */
    public function defaultValidationRules(): array
    {
        return [$this->machineName => 'exists:'.$this->option('model')::tableName().',id'];
    }
}