<?php 

namespace Pingu\Field\Displayers\Options;

use Pingu\Field\Support\DisplayOptions;
use Pingu\Forms\Support\Fields\Select;
use Pingu\Forms\Support\Fields\TextInput;

class DefaultDatetimeOptions extends DisplayOptions
{
    /**
     * Options defined by this class
     * @var array
     */
    protected $optionNames = ['format'];

    /**
     * @var array
     */
    protected $labels = [
        'format' => 'Format',
    ];

    /**
     * @var array
     */
    protected $values = [
        'format' => 'Y/m/d - H:i',
        'custom' => ''
    ];

    /**
     * @inheritDoc
     */
    public function toFormElements(): array
    {
        return [
            new Select(
                'format',
                [
                    'default' => $this->format,
                    'required' => true,
                    'items' => $this->getFormats()
                ]
            ),
            new TextInput(
                'custom',
                [
                    'default' => $this->custom
                ]
            )
        ];
    }

    /**
     * Get available formats for dates
     * 
     * @return array
     */
    protected function getFormats(): array
    {
        return [
            'Y/m/d - H:i' => 'Short : '.date('Y/m/d - H:i'),
            'l, F d, Y - H:i' => 'Long : '.date('l, F d, Y - H:i'),
            'Y' => 'Year only : '.date('Y'),
            'Y/m/d' => 'Date only : '.date('Y/m/d'),
            'H:i:s' => 'Time only : '.date('H:i:s'),
            'custom' => 'Custom (fill underneath)'
        ];
    }

    /**
     * @inheritDoc
     */
    public function friendlyDescription(): string
    {
        if ($this->format == 'custom') {
            return $this->label('format').': '.date($this->custom);
        } else {
            return $this->label('format').': '.date($this->format);
        }
    }

    /**
     * @inheritDoc
     */
    public function getValidationRules(): array
    {
        return [
            'format' => 'required|string',
            'custom' => 'string'
        ];
    }
}