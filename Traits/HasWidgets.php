<?php

namespace Pingu\Field\Traits;

trait HasWidgets
{
    protected static $availableWidgets = [];

    /**
     * Register widgets in form field facade
     */
    public static function registerWidgets()
    {
        \FormField::registerWidgets(static::class, static::$availableWidgets);
    }

    /**
     * @inheritDoc
     */
    public static function availableWidgets(): array
    {
        $out = [];
        foreach (static::$availableWidgets as $widget) {
            $out[$widget] = $widget::friendlyName();
        }
        return $out;
    }

    public static function addWidget(string $widget)
    {
        static::$availableWidgets[] = $widget;
    }

    /**
     * @inheritDoc
     */
    public static function defaultWidget(): string
    {
        return \FormField::defaultWidget(static::class);
    }

    /**
     * @inheritDoc
     */
    public function setWidget(string $widget)
    {
        $this->widget = $widget;
    }

    /**
     * @inheritDoc
     */
    public function widget(): string
    {
        return $this->widget;
    }
}