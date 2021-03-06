<?php

namespace Pingu\Field\Support;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Request;
use Pingu\Entity\Entities\DisplayField;
use Pingu\Field\Contracts\FieldContract;
use Pingu\Field\Contracts\FieldDisplayerContract;
use Pingu\Field\Forms\DisplayOptionsForm;
use Pingu\Forms\Support\Form;

class DisplayOptions implements Arrayable
{
    /**
     * Options defined by this class
     * @var array
     */
    protected $optionNames = [];

    /**
     * @var array
     */
    protected $labels = [];

    /**
     * @var array
     */
    protected $values = [];

    /**
     * Casts for values
     * supported : bool, int, float
     * @var array
     */
    protected $casts = [];

    /**
     * @var FieldDisplayerContract
     */
    protected $displayer;

    /**
     * Constructor
     * 
     * @param array  $values
     */
    public function __construct(FieldDisplayerContract $displayer)
    {
        $this->displayer = $displayer;
    }

    /**
     * Display field getter 
     * 
     * @return FieldDisplayerContract
     */
    public function getDisplayer(): FieldDisplayerContract
    {
        return $this->displayer;
    }

    /**
     * Display field getter
     * 
     * @return DisplayField
     */
    public function getDisplayField(): DisplayField
    {
        return $this->displayer->getDisplayField();
    }

    /**
     * Field getter
     * 
     * @return FieldContract
     */
    public function getField(): FieldContract
    {
        return $this->displayer->getField();
    }

    /**
     * Get form elements for the options
     * 
     * @return array
     */
    public function toFormElements(): array
    {
        return [];
    }

    /**
     * Validation rules for the edit options request
     * 
     * @return array
     */
    public function getValidationRules(): array
    {
        return [];
    }

    /**
     * Validation messages for the options
     * 
     * @return array
     */
    public function getValidationMessages(): array
    {
        return [];
    }

    /**
     * Set the values
     * 
     * @param array $values
     */
    public function setValues(array $values): DisplayOptions
    {
        $this->values = $values;
        return $this;
    }

    /**
     * Friendly description for the options
     * 
     * @return string
     */
    public function friendlyDescription(): string
    {
        $out = '';
        foreach ($this->optionNames as $name) {
            $out .= '<p>'.$this->label($name).': '.$this->friendlyValue($name).'</p>';
        }
        return $out;
    }

    /**
     * Label for an option name
     * 
     * @param string $name
     * 
     * @return string
     */
    public function label(string $name): string
    {
        return $this->labels[$name] ?? '';
    }

    /**
     * Value for an option name
     * 
     * @param string $name 
     * 
     * @return mixed
     */
    public function get(string $name)
    {
        return $this->values[$name] ?? null;
    }

    /**
     * Friendly value for an option name
     * 
     * @param string $name 
     * 
     * @return mixed
     */
    public function friendlyValue(string $name)
    {
        if (!isset($this->values[$name])) {
            return '';
        }
        return $this->castFriendlyValue($name, $this->values[$name]);
    }

    /**
     * Get the form to edit the options
     * 
     * @param array  $action
     * 
     * @return Form
     */
    public function getEditForm(array $action): Form
    {
        return new DisplayOptionsForm($action, $this);
    }

    /**
     * Validate a edit option request
     * 
     * @param Request $request
     */
    public function validate(Request $request)
    {
        $values = $request->except(['_token', '_theme']);
        $rules = $this->getValidationRules();
        $messages = $this->getValidationMessages();
        $validator = \Validator::make($values, $rules, $messages);
        $validator->validate();
        $this->values = $this->castValues($validator->validated());
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'values' => $this->castFormValues($this->values),
            'description' => $this->friendlyDescription()
        ];
    }

    /**
     * Values getter
     * 
     * @return array
     */
    public function values(): array
    {
        return $this->values;
    }

    /**
     * Values getter for a form
     * 
     * @return array
     */
    public function formValues(): array
    {
        return $this->castFormValues($this->values);
    }

    /**
     * Casts an array of values
     * 
     * @param  array  $values
     * @return array
     */
    public function castValues(array $values): array
    {
        foreach ($values as $name => $value) {
            switch ($this->casts[$name] ?? '') {
                case 'bool':
                    $values[$name] = (bool)$value;
                    break;
                case 'boolean':
                    $values[$name] = (bool)$value;
                    break;
                case 'int':
                    $values[$name] = (int)$value;
                    break;
                case 'float':
                    $values[$name] = (float)$value;
                    break;
                default:
                    break;
            }
        }
        return $values;
    }

    /**
     * Casts an array of values for a form
     * 
     * @param array $values
     * 
     * @return array
     */
    protected function castFormValues(array $values): array
    {
        foreach ($values as $name => $value) {
            switch ($this->casts[$name] ?? '') {
                case 'bool':
                    $values[$name] = (int)$value;
                    break;
                case 'boolean':
                    $values[$name] = (int)$value;
                    break;
                default:
                    break;
            }
        }
        return $values;
    }

    /**
     * Cast a friendly value
     * 
     * @param string $name
     * @param mixed $value
     * 
     * @return mixed
     */
    protected function castFriendlyValue(string $name, $value)
    {
        switch ($this->casts[$name] ?? '') {
            case 'bool':
                return $value ? 'Yes' : 'No';
            case 'boolean':
                return $value ? 'Yes' : 'No';
        }
        return $value;
    }

    /**
     * Forward getter to option vaue
     * 
     * @param $name
     * 
     * @return mixed
     */
    public function __get($name)
    {
        return $this->get($name);
    }
}