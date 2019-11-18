<?php

namespace Pingu\Field\Entities\Fields;

use Pingu\Field\BaseFields\Boolean;
use Pingu\Field\BaseFields\Text;
use Pingu\Field\Support\FieldRepository\BundleFieldFieldRepository;

class FieldSlugFields extends BundleFieldFieldRepository
{
    /**
     * @inheritDoc
     */
    protected function fields(): array
    {
        return [
            new Text('default'),
            new Boolean('required'),
            new Text(
                'from',
                [
                    'helper' => 'Generate slug from another field, enter its name (optional)'
                ]
            ),
        ];
    }
}