<?php

namespace Pingu\Field\Entities\Fields;

use Pingu\Field\BaseFields\Boolean;
use Pingu\Field\BaseFields\Datetime;
use Pingu\Field\BaseFields\Text;
use Pingu\Field\Support\FieldRepository\BundleFieldFieldRepository;

class FieldDatetimeFields extends BundleFieldFieldRepository
{
    /**
     * @inheritDoc
     */
    protected function fields(): array
    {
        return [
            new Boolean(
                'setToCurrent',
                [
                    'label' => 'Default to current date'
                ]
            ),
            new Text(
                'format',
                [
                    'helper' => 'A valid <a target="_blank" href="https://www.php.net/manual/en/function.date.php">Php format</a>', 
                    'default' => $this->object->getFormat(),
                    'required' => true
                ]
            ),
            new Boolean('required')
        ];
    }

    /**
     * @inheritDoc
     */
    protected function rules(): array
    {
        return [
            'required' => 'boolean',
            'setToCurrent' => 'boolean',
        ];
    }

    /**
     * @inheritDoc
     */
    protected function messages(): array
    {
        return [];
    }
}