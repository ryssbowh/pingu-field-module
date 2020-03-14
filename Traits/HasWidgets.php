<?php

namespace Pingu\Field\Traits;

trait HasWidgets
{
    /**
     * Default available Form Field classes
     * 
     * @var array
     */
    protected static $availableWidgets = [];

    /**
     * Form Field class for this Field
     * 
     * @var string
     */
    protected $widget;

    /**
     * Register widgets in form field facade
     */
    protected static function registerWidgets()
    {
        \FormField::registerWidgets(static::class, static::$availableWidgets);
    }

    /**
     * @inheritDoc
     */
    public static function availableWidgets(): array
    {
        return \FormField::availableWidgets(static::class);
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
        return $this->widget ?? $this->defaultWidget();
    }
}