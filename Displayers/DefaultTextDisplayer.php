<?php

namespace Pingu\Field\Displayers;

use Pingu\Field\Support\FieldDisplayer;

class DefaultTextDisplayer extends FieldDisplayer
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
        return 'text.default';
    }

    /**
     * @ineritDoc
     */
    public static function hasOptions(): bool
    {
        return false;
    }

    /**
     * @ineritDoc
     */
    public static function optionsClass(): string
    {
        return '';
    }
}