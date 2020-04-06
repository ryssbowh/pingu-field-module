<?php 

namespace Pingu\Field\Displayers\Options;

use Pingu\Field\Support\DisplayOptions;
use Pingu\Forms\Support\Fields\TextInput;

class DefaultBooleanOptions extends DisplayOptions
{
    /**
     * Options defined by this class
     * @var array
     */
    protected $optionNames = ['yesLabel', 'noLabel'];

    /**
     * @var array
     */
    protected $labels = [
        'yesLabel' => 'Label when on',
        'noLabel' => 'Label when off'
    ];

    /**
     * @var array
     */
    protected $values = [
        'yesLabel' => 'Yes',
        'noLabel' => 'No',
    ];

    /**
     * @inheritDoc
     */
    public function toFormElements(): array
    {
        return [
            new TextInput('yesLabel', [
                'default' => $this->value('yesLabel')
            ]),
            new TextInput('noLabel', [
                'default' => $this->value('noLabel')
            ])
        ];
    }

    /**
     * @inheritDoc
     */
    public function getValidationRules(): array
    {
        return [
            'yesLabel' => 'string',
            'noLabel' => 'string'
        ];
    }
}