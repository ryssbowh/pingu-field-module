<?php

namespace Pingu\Field\Displayers;

use Pingu\Entity\Support\Entity;
use Pingu\Field\Support\FieldDisplayer;

class DefaultFloatDisplayer extends FieldDisplayer
{
    /**
     * @ineritDoc
     */
    public static function friendlyName(): string
    {
        return 'Default';
    }

    /**
     * @ineritDoc
     */
    public static function machineName(): string
    {
        return 'float-default';
    }

    public function systemView(): string
    {
        return 'field@fields.float-default';
    }

    public function getFieldValue($value)
    {
        return $value;
    }
}