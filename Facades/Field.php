<?php

namespace Pingu\Field\Facades;

use Illuminate\Support\Facades\Facade;

class Field extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'field.field';
    }

}