<?php

namespace Pingu\Field\Displayers;

use Pingu\Entity\Support\Entity;
use Pingu\Field\Displayers\Options\DefaultDatetimeOptions;
use Pingu\Field\Support\FieldDisplayerWithOptions;

class DefaultDateDisplayer extends FieldDisplayerWithOptions
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
        return 'date-default';
    }

    /**
     * @ineritDoc
     */
    public static function optionsClass(): string
    {
        return DefaultDatetimeOptions::class;
    }

    /**
     * @inheritDoc
     */
    public function systemView(): string
    {
        return 'field@fields.date-default';
    }

    /**
     * @inheritDoc
     */
    public function getFieldValue($value)
    {
        $format = $this->options()->format;
        if ($format == 'custom') {
            $format = $this->options()->custom;
        }
        return $value->format($format);
    }
}