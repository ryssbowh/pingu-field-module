<?php

namespace Pingu\Field\Displayers;

use Pingu\Entity\Support\Entity;
use Pingu\Field\Displayers\Options\DefaultModelOptions;
use Pingu\Field\Support\FieldDisplayerWithOptions;

class DefaultModelDisplayer extends FieldDisplayerWithOptions
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
        return 'model-default';
    }

    /**
     * @ineritDoc
     */
    public static function optionsClass(): string
    {
        return DefaultModelOptions::class;
    }

    /**
     * @inheritDoc
     */
    public function systemView(): string
    {
        return 'field@fields.model-default';
    }

    /**
     * @inheritDoc
     */
    public function getFieldValue($value)
    {
        return $value->{$this->option('field')};
    }
}