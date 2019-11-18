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
            new Boolean('required')
        ];
    }
}