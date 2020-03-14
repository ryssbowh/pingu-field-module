<?php

namespace Pingu\Field\Contracts; 

use Pingu\Forms\Support\Field;
use Pingu\Forms\Support\FormElement;

interface HasWidgetsContracts
{
    /**
     * List of form elements that are available to render this field
     * 
     * @return array
     */
    public static function availableWidgets(): array;

    /**
     * Default form element to render this field
     * 
     * @return string
     */
    public static function defaultWidget(): string;

    /**
     * Set the widget to render this field
     *
     * @param string $widget
     */
    public function setWidget(string $widget);

    /**
     * Widget getter, returns form field class
     * 
     * @return string
     */
    public function widget(): string;

}