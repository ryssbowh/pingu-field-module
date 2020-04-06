<?php

namespace Pingu\Field\Displayers;

use Pingu\Entity\Support\Entity;
use Pingu\Field\Displayers\Options\DefaultUrlOptions;
use Pingu\Field\Support\FieldDisplayerWithOptions;

class DefaultUrlDisplayer extends FieldDisplayerWithOptions
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
        return 'url-default';
    }

    /**
     * @ineritDoc
     */
    public static function optionsClass(): string
    {
        return DefaultUrlOptions::class;
    }

    /**
     * @inheritDoc
     */
    public function systemView(): string
    {
        return 'field@fields.url-default';
    }

    /**
     * @inheritDoc
     */
    public function getFieldValue($value)
    {
        return '<a href="'.$value.'"'.($this->options()->newWindow ? ' target="_blank"' : '').'>'.$value.'</a>';
    }
}