<?php

namespace Pingu\Field\Entities\Fields;

use Pingu\Field\BaseFields\Boolean;
use Pingu\Field\BaseFields\Integer;
use Pingu\Field\BaseFields\_Float;
use Pingu\Field\Support\FieldRepository\BundleFieldFieldRepository;

class FieldFloatFields extends BundleFieldFieldRepository
{
    /**
     * @inheritDoc
     */
    protected function fields(): array
    {
        return [
            new _Float(
                'default',
                [
                    'step' => 0.000001
                ]
            ),
            new Integer(
                'precision',
                [
                    'required' => true,
                    'default' => 2
                ]
            ),
            new Boolean('required')
        ];
    }
}