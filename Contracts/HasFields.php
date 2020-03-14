<?php

namespace Pingu\Field\Contracts;

use Pingu\Field\Contracts\FieldContract;
use Pingu\Field\Contracts\FieldRepository;
use Pingu\Field\Contracts\FieldsValidator;
use Pingu\Field\Support\FieldLayout;

interface HasFields extends DefinesFields
{
    /**
     * Field names that can be filtered on
     * 
     * @return array
     */
    public function getFilterable(): array;
}