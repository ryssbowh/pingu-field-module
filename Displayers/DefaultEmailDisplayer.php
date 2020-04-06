<?php

namespace Pingu\Field\Displayers;

use Pingu\Entity\Support\Entity;
use Pingu\Field\Displayers\Options\DefaultEmailOptions;
use Pingu\Field\Support\FieldDisplayerWithOptions;

class DefaultEmailDisplayer extends FieldDisplayerWithOptions
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
        return 'email-default';
    }

    /**
     * @ineritDoc
     */
    public static function optionsClass(): string
    {
        return DefaultEmailOptions::class;
    }

    /**
     * @inheritDoc
     */
    public function systemView(): string
    {
        return 'field@fields.email-default';
    }

    /**
     * @inheritDoc
     */
    public function getFieldValue($value)
    {
        if ($this->options()->linked) {
            return '<a href="mailto:'.$value.'">'.$value.'</a>';
        }
        return $value;
    }
}