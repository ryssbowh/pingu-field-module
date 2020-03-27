<?php 

namespace Pingu\Field\Displayers;

use Pingu\Field\Displayers\Options\TrimmedTextOptions;
use Pingu\Field\Support\FieldDisplayer;

class TrimmedTextDisplayer extends FieldDisplayer
{
    /**
     * @ineritDoc
     */
    public static function friendlyName(): string
    {
        return 'Trimmed';
    }

    /**
     * @ineritDoc
     */
    public static function machineName(): string
    {
        return 'text.trimmed';
    }

    /**
     * @ineritDoc
     */
    public static function hasOptions(): bool
    {
        return true;
    }

    /**
     * @ineritDoc
     */
    public static function optionsClass(): string
    {
        return TrimmedTextOptions::class;
    }
}