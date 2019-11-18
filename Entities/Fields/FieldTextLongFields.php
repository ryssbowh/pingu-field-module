<?php

namespace Pingu\Field\Entities\Fields;

use Pingu\Field\BaseFields\Boolean;
use Pingu\Field\BaseFields\LongText;
use Pingu\Field\Support\FieldRepository\BundleFieldFieldRepository;

class FieldTextLongFields extends BundleFieldFieldRepository
{
    /**
     * @inheritDoc
     */
    protected function fields(): array
    {
        return [
            new LongText('default'),
            new Boolean('required')
        ];
    }
}