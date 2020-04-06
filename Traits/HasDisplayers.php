<?php

namespace Pingu\Field\Traits;

trait HasDisplayers
{
    /**
     * Displayers for this Field
     * 
     * @var string
     */
    protected static $displayers = [];

    /**
     * Register widgets in form field facade
     */
    protected static function registerDisplayers()
    {
        \FieldDisplayer::append(static::class, static::$displayers);
    }

    /**
     * @inheritDoc
     */
    public static function availableDisplayers(): array
    {
        return \FieldDisplayer::getForField(static::class);
    }

    /**
     * @inheritDoc
     */
    public static function defaultDisplayer($asClass = true)
    {
        return \FieldDisplayer::defaultDisplayer(static::class, $asClass);
    }
}