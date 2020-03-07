<?php

namespace Pingu\Field\Support;

use Pingu\Core\Entities\BaseModel;
use Pingu\Field\Contracts\FieldContract;
use Pingu\Field\Exceptions\FieldsException;
use Pingu\Field\Traits\HasWidgets;
use Pingu\Forms\Support\Field;
use Pingu\Forms\Support\FormElement;

abstract class BaseField implements FieldContract
{
    use HasWidgets;

    protected $machineName;
    protected $options;
    protected $widget;
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

    protected function init(array $options)
    {
        $this->options = collect(array_merge($this->defaultOptions(), $options));
        $this->widget = $widget ?? $this->defaultWidget();
    }

    /**
     * Set the model this field is attached to
     * 
     * @param BaseModel $model
     */
    public function setModel(BaseModel $model)
    {
        $this->model = $model;
    }

    /**
     * @inheritDoc
     */
    public function definesRelation()
    {
        return false;
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
    public function toFormElement(): FormElement
    {
        $class = \FormField::getRegisteredField($this->widget());
        $options = $this->formFieldOptions();
        $field = new $class($this->machineName, $options);
        $field->setValue($this->value(false));
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
    public function value(bool $casted = true)
    {
        $value = ($this->model and $this->model->exists) ? 
            $this->model->getFormValue($this->machineName) : 
            $this->defaultValue();
        if ($casted) {
            return $value;
        }
        return $this->formValue($value);
    }

    /**
     * @inheritDoc
     */
    public function defaultValidationMessages(): array
    {
        return [];
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
    protected function formFieldOptions(): array
    {
        return $this->options->toArray();
    }
}