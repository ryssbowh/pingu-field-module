<?php

namespace Pingu\Field\Support;

use Pingu\Core\Entities\BaseModel;
use Pingu\Field\Contracts\FieldContract;
use Pingu\Field\Exceptions\FieldsException;
use Pingu\Field\Traits\HasDisplayers;
use Pingu\Field\Traits\HasFilterWidgets;
use Pingu\Field\Traits\HasWidgets;
use Pingu\Forms\Support\Field;
use Pingu\Forms\Support\FormElement;

abstract class BaseField implements FieldContract
{
    use HasWidgets, HasFilterWidgets, HasDisplayers;

    protected $machineName;
    protected $options;
    protected $model;
    protected $requiredOptions = [];

    public function __construct(string $machineName, array $options = [], ?string $name = null, ?string $widget = null)
    {
        foreach ($this->requiredOptions as $attr) {
            if (!isset($options[$attr])) {
                throw FieldsException::missingOption($machineName, $attr);
            }
        }
        if (is_null($name)) {
            $name = form_label($machineName);
        }
        $this->machineName = $machineName;
        $this->name = $name;
        $this->init($options);
    }

    /**
     * @inheritDoc
     */
    public function saveOnModel(BaseModel $model, $value): bool
    {
        $model->{$this->machineName} = $value;
        return true;
    }

    /**
     * @inheritDoc
     */
    public function definesSyncableRelation(): bool
    {
        return false;
    }

    /**
     * @inheritDoc
     */
    public function filterable(): bool
    {
        return true;
    }

    protected function init(array $options)
    {
        $this->options = collect(array_merge($this->defaultOptions(), $options));
    }

    /**
     * @inheritDoc
     */
    public function fixedCardinality()
    {
        return 1;
    }

    /**
     * @inheritDoc
     */
    public function cardinality(): int
    {
        return 1;
    }

    /**
     * @inheritDoc
     */
    public function machineName(): string
    {
        return $this->machineName;
    }

    /**
     * @inheritDoc
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * Does this field has an option called $name
     * 
     * @param string $name
     * 
     * @return boolean
     */
    public function hasOption(string $name): bool
    {
        return $this->options->has($name);
    }

    /**
     * @inheritDoc
     */
    public function defaultValue()
    {
        if ($this->hasOption('default')) {
            return $this->option('default');
        }
    }

    /**
     * Sets/gets an option
     * 
     * @param string $name
     * @param mixed  $value
     * 
     * @return Form|mixed
     */
    public function option(string $name, $value = null)
    {
        if (!is_null($value)) {
            $this->options->put($name, $value);
            return $this;
        }
        return $this->options->get($name);
    }

    /**
     * @inheritDoc
     */
    public function toFormElement($value): FormElement
    {
        $class = \FormField::getRegisteredField($this->widget());
        $options = $this->formFieldOptions();
        $options['default'] = $value;
        $field = new $class($this->machineName, $options);
        return $field;
    }

    /**
     * @inheritDoc
     */
    public function formValue(BaseModel $model)
    {
        $value = $model->exists ? $model->getFormValue($this->machineName()) : $this->defaultValue();
        return $this->castToFormValue($value);
    }

    /**
     * @inheritDoc
     */
    public function toFilterFormElement(): FormElement
    {
        $class = \FormField::getRegisteredField($this->filterWidget());
        $options = $this->formFilterFieldOptions();
        $field = new $class($this->machineName, $options);
        return $field;
    }

    /**
     * @inheritDoc
     */
    public function defaultValidationRules(): array
    {
        return [$this->machineName => ''];
    }

    /**
     * @inheritDoc
     */
    public function defaultValidationMessages(): array
    {
        return [];
    }

    /**
     * Registers this field
     */
    public static function register()
    {
        static::registerWidgets();
        static::registerFilterWidgets();
        static::registerDisplayers();
    }

    /**
     * Default options for this field
     * 
     * @return array
     */
    protected function defaultOptions(): array
    {
        return [
            'label' => $this->name,
            'cardinality' => 1
        ];
    }

    /**
     * Field options to be passed to a FormElement
     * 
     * @return array
     */
    public function formFieldOptions(int $index = 0): array
    {
        return $this->options->toArray();
    }

    /**
     * Field options to be passed to a FormElement for a filter form
     * 
     * @return array
     */
    public function formFilterFieldOptions(): array
    {
        $options = $this->options->toArray();
        $options['htmlName'] = 'filters['.$this->machineName.']';
        $options['required'] = false;
        $options['disabled'] = false;
        return $options;
    }
}