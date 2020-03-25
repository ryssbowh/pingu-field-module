<?php

namespace Pingu\Field\Traits;

trait HasDisplayers
{
    /**
     * Displayers this Field
     * 
     * @var string
     */
    protected static $displayers = [];

    /**
     * Register widgets in form field facade
     */
    protected static function registerDisplayers()
    {
        \FieldDisplay::appendFieldDisplayer(static::class, static::$displayers);
    }

    /**
     * @inheritDoc
     */
    public static function availableDisplayers(): array
    {
        return \FieldDisplay::getDisplayersForField(static::class);
    }

    public static function defaultDisplayer($asClass = true): string
    {
        return \FieldDisplay::defaultDisplayerForField(static::class, $asClass);
    }
}