<?php

namespace Pingu\Field\Support;

use Pingu\Core\Entities\BaseModel;
use Pingu\Field\Contracts\FieldContract;
use Pingu\Field\Exceptions\FieldsException;
use Pingu\Forms\Support\Field;
use Pingu\Forms\Support\FormElement;

abstract class BaseField implements FieldContract
{
    protected $machineName;
    protected $options;
    protected $formFieldClass;
    protected $model;
    protected $requiredOptions = [];

    public function __construct(string $machineName, array $options = [], ?string $name = null, ?string $formFieldClass = null)
    {
        foreach ($this->requiredOptions as $attr) {
            if (!isset($options[$attr])) {
                throw FieldsException::missingOption($machineName, $attr);
            }
        }
        if (is_null($name)) {
            $this->name = ucfirst($machineName);
        }
        $this->machineName = $machineName;
        $this->name = $name;
        $this->options = collect(array_merge($this->defaultOptions(), $options));
        $this->formFieldClass = $formFieldClass ?? $this->defaultFormFieldClass();
        $this->init();
    }

    /**
     * Initialize this field, called in the constructor
     */
    protected function init()
    {
    }

    /**
     * Default form class to turn this field into a FormElement
     * 
     * @return string
     */
    abstract protected function defaultFormFieldClass(): string;

    /**
     * @inheritDoc
     */
    public abstract function castValue($value);

    /**
     * @inheritDoc
     */
    public abstract function formValue($value);

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
        $class = $this->formFieldClass;
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
        $value = ($this->model and $this->model->exists) ? $this->model->getFormValue($this->machineName) : $this->defaultValue();
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
            'label' => $this->name
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