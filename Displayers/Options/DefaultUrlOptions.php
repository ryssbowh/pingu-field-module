<?php 

namespace Pingu\Field\Displayers\Options;

use Pingu\Field\Support\DisplayOptions;
use Pingu\Forms\Support\Fields\Checkbox;

class DefaultUrlOptions extends DisplayOptions
{
    /**
     * Options defined by this class
     * @var array
     */
    protected $optionNames = ['newWindow'];

    /**
     * @var array
     */
    protected $labels = [
        'newWindow' => 'Opens in new window'
    ];

    /**
     * @var array
     */
    protected $values = [
        'newWindow' => false
    ];

    /**
     * @var array
     */
    protected $casts = [
        'newWindow' => 'bool'
    ];

    /**
     * @inheritDoc
     */
    public function toFormElements(): array
    {
        return [
            new Checkbox('newWindow', [
                'default' => $this->newWindow,
                'label' => $this->label('newWindow')
            ])
        ];
    }

    /**
     * @inheritDoc
     */
    public function getValidationRules(): array
    {
        return [
            'newWindow' => 'boolean'
        ];
    }
}