<?php

namespace Pingu\Field\Entities\Fields;

use Pingu\Field\BaseFields\Integer;
use Pingu\Field\BaseFields\Text;
use Pingu\Field\BaseFields\_List;
use Pingu\Field\Support\FieldRepository\BaseFieldRepository;

class BundleFieldFields extends BaseFieldRepository
{
    /**
     * @inheritDoc
     */
    protected function fields(): array
    {
        return [
            new Text(
                'name',
                [
                    'required' => true
                ]
            ),
            new Text(
                'machineName',
                [
                    'helper' => 'Unique machine name, cannot be edited',
                    'required' => true
                ]
            ),
            new Text(
                'helper',
                [
                    'helper' => 'Describe this field to the user'
                ]
            ),
            new _List(
                '_cardinality_select',
                [
                    'label' => 'Number of values',
                    'items' => [
                        'number' => 'Limited',
                        '-1' => 'Unlimited'
                    ],
                    'default' => 'number'
                ]
            ),
            new Integer(
                'cardinality', 
                [
                    'label' => false,
                    'default' => 1
                ]
            ),
        ];
    }
}