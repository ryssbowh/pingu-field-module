<?php

namespace Pingu\Field\Facades;

use Illuminate\Support\Facades\Facade;

class FieldDisplay extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'field.display';
    }

}