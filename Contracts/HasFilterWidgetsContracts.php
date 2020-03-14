<?php

namespace Pingu\Field\Contracts; 

use Pingu\Forms\Support\Field;
use Pingu\Forms\Support\FormElement;

interface HasFilterWidgetsContracts
{
    /**
     * List of form elements that are available to render this field as a filter
     * 
     * @return array
     */
    public static function availableFilterWidgets(): array;

    /**
     * Default form element to render this field as a widget
     * 
     * @return string
     */
    public static function defaultFilterWidget(): string;

    /**
     * Set the widget to render this field as a filter
     *
     * @param string $widget
     */
    public function setWidget(string $widget);

    /**
     * Filter widget getter, returns form field class
     * 
     * @return string
     */
    public function filterWidget(): string;

}