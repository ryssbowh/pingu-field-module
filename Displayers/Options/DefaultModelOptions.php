<?php 

namespace Pingu\Field\Displayers\Options;

use Pingu\Field\Support\DisplayOptions;
use Pingu\Forms\Support\Fields\Checkbox;
use Pingu\Forms\Support\Fields\Select;

class DefaultModelOptions extends DisplayOptions
{
    /**
     * Options defined by this class
     * @var array
     */
    protected $optionNames = ['field'];

    /**
     * @var array
     */
    protected $labels = [
        'field' => 'Field to display'
    ];

    /**
     * @var array
     */
    protected $values = [
        'field' => 'id'
    ];

    /**
     * Get the model fields
     * 
     * @return array
     */
    public function getModelFields(): array
    {
        $model = $this->getField()->option('model');
        $out = [];
        foreach ((new $model)->fields()->get() as $field) {
            $out[$field->machineName()] = $field->name();
        }
        return $out;
    }

    /**
     * @inheritDoc
     */
    public function toFormElements(): array
    {
        return [
            new Select('field', [
                'label' => $this->label('field'),
                'items' => $this->getModelFields()
            ])
        ];
    }

    /**
     * @inheritDoc
     */
    public function getValidationRules(): array
    {
        return [
            'field' => 'required|in:'.implode(',', array_keys($this->getModelFields()))
        ];
    }
}