<?php

namespace Pingu\Field\Entities\Fields;

use Pingu\Field\BaseFields\Boolean;
use Pingu\Field\BaseFields\Text;
use Pingu\Field\Support\FieldRepository\BundleFieldFieldRepository;

class FieldTimeFields extends BundleFieldFieldRepository
{
    protected function fields(): array
    {
        return [
            new Boolean(
                'setToCurrent',
                [
                    'label' => 'Default to current time'
                ]
            ),
            new Text(
                'format',
                [
                    'helper' => 'A valid time <a target="_blank" href="https://www.php.net/manual/en/function.date.php">Php format</a>', 
                    'default' => $this->object->getFormat(),
                    'required' => true
                ]
            ),
            new Boolean('required')
        ];
    }
}