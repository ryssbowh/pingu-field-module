<?php

namespace Pingu\Field\Entities\Fields;

use Pingu\Field\BaseFields\Text;
use Pingu\Field\Support\FieldRepository\BaseFieldRepository;

class FormLayoutGroupFields extends BaseFieldRepository
{
    protected function fields(): array
    {
        return [
            new Text('name'),
            new Text('object')
        ];
    }
}