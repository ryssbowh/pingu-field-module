<?php

namespace Pingu\Field\Entities\Fields;

use Pingu\Field\BaseFields\Boolean;
use Pingu\Field\BaseFields\Text;
use Pingu\Field\Support\FieldRepository\BundleFieldFieldRepository;

class FieldUrlFields extends BundleFieldFieldRepository
{
    /**
     * @inheritDoc
     */
    protected function fields(): array
    {
        return [
            new Text('default'),
            new Boolean('required')
        ];
    }
}