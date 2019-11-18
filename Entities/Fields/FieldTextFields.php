<?php

namespace Pingu\Field\Entities\Fields;

use Pingu\Field\BaseFields\Boolean;
use Pingu\Field\BaseFields\Integer;
use Pingu\Field\BaseFields\Text;
use Pingu\Field\Support\FieldRepository\BundleFieldFieldRepository;

class FieldTextFields extends BundleFieldFieldRepository
{
    /**
     * @inheritDoc
     */
    protected function fields(): array
    {
        return [
            new Text('default'),
            new Boolean('required'),
            new Integer(
                'maxLength',
                [
                    'label' => 'Maximum length',
                    'default' => 255,
                    'max' => 255,
                    'min' => 1
                ]
            )
        ];
    }
}