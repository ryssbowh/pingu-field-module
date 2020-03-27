<?php

namespace Pingu\Field\Facades;

use Illuminate\Support\Facades\Facade;

class FieldDisplayer extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'field.fieldDisplayer';
    }

}