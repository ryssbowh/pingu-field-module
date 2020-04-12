<?php 

namespace Pingu\Field\Displayers\Options;

use Pingu\Field\Support\DisplayOptions;
use Pingu\Forms\Support\Fields\NumberInput;

class TrimmedTextOptions extends DisplayOptions
{
    /**
     * Options defined by this class
     * @var array
     */
    protected $optionNames = ['limit'];

    /**
     * @var array
     */
    protected $labels = [
        'limit' => 'Character limit'
    ];

    /**
     * @var array
     */
    protected $values = [
        'limit' => 600
    ];

    /**
     * @inheritDoc
     */
    public function toFormElements(): array
    {
        return [
            new NumberInput('limit', [
                'min' => 0,
                'default' => $this->limit
            ])
        ];
    }

    /**
     * @inheritDoc
     */
    public function getValidationRules(): array
    {
        return [
            'limit' => 'required|integer|min:0'
        ];
    }
}