<?php

namespace Pingu\Field\Contracts;

interface HasFields extends DefinesFields
{
    /**
     * Field names that can be filtered on
     * 
     * @return array
     */
    public function getFilterable(): array;
}