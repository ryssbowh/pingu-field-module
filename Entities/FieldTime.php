<?php

namespace Pingu\Field\Entities;

use Pingu\Entity\Entities\Entity;

class FieldTime extends FieldDatetime
{
    protected $defaultFormat = "H:i:s";
    protected $dbFormat = 'H:i:s';

    /**
     * @inheritDoc
     */
    public static function friendlyName(): string 
    {
        return 'Time';
    }
}