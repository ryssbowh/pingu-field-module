<?php

namespace Pingu\Field\Displayers;

use Pingu\Entity\Support\Entity;
use Pingu\Field\Displayers\Options\DefaultBooleanOptions;
use Pingu\Field\Support\FieldDisplayerWithOptions;

class DefaultBooleanDisplayer extends FieldDisplayerWithOptions
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
        return 'boolean-default';
    }

    /**
     * @ineritDoc
     */
    public static function optionsClass(): string
    {
        return DefaultBooleanOptions::class;
    }

    /**
     * @inheritDoc
     */
    public function systemView(): string
    {
        return 'field@fields.boolean-default';
    }

    /**
     * @inheritDoc
     */
    public function getFieldValue($value)
    {
        return $value ? $this->options()->yesLabel : $this->options()->noLabel;
    }
}