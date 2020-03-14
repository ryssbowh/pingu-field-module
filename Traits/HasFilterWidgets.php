<?php

namespace Pingu\Field\Traits;

trait HasFilterWidgets
{
    /**
     * Default available Form Field classes to display this field as a filter
     * 
     * @var array
     */
    protected static $availableFilterWidgets = [];

    /**
     * Form Field class to display this field as a filter
     * 
     * @var string
     */
    protected $filterWidget;

    /**
     * Register filter widgets in form field facade
     */
    protected static function registerFilterWidgets()
    {
        \FormField::registerFilterWidgets(static::class, static::$availableFilterWidgets);
    }

    /**
     * @inheritDoc
     */
    public static function availableFilterWidgets(): array
    {
        return \FormField::availableFilterWidgets(static::class);
    }

    /**
     * @inheritDoc
     */
    public static function defaultFilterWidget(): string
    {
        return \FormField::defaultFilterWidget(static::class);
    }

    /**
     * @inheritDoc
     */
    public function setFilterWidget(string $widget)
    {
        $this->filterWidget = $widget;
    }

    /**
     * @inheritDoc
     */
    public function filterWidget(): string
    {
        return $this->filterWidget ?? $this->defaultFilterWidget();
    }
}