<?php 

namespace Pingu\Field\Displayers\Options;

use Pingu\Field\Support\DisplayOptions;
use Pingu\Forms\Support\Fields\Checkbox;

class DefaultEmailOptions extends DisplayOptions
{
    /**
     * Options defined by this class
     * @var array
     */
    protected $optionNames = ['linked'];

    /**
     * @var array
     */
    protected $labels = [
        'linked' => 'Linked'
    ];

    /**
     * @var array
     */
    protected $values = [
        'linked' => true
    ];

    /**
     * @var array
     */
    protected $casts = [
        'linked' => 'bool'
    ];

    /**
     * @inheritDoc
     */
    public function toFormElements(): array
    {
        return [
            new Checkbox('linked', [
                'default' => $this->linked,
                'label' => 'Turn into a linked email'
            ])
        ];
    }

    /**
     * @inheritDoc
     */
    public function getValidationRules(): array
    {
        return [
            'linked' => 'boolean'
        ];
    }
}