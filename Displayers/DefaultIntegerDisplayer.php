<?php

namespace Pingu\Field\Displayers;

use Pingu\Entity\Support\Entity;
use Pingu\Field\Support\FieldDisplayer;

class DefaultIntegerDisplayer extends FieldDisplayer
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
        return 'integer-default';
    }

    public function systemView(): string
    {
        return 'field@fields.integer-default';
    }

    public function getFieldValue($value)
    {
        return $value;
    }
}