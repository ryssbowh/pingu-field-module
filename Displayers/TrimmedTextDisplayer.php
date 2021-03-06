<?php 

namespace Pingu\Field\Displayers;

use Pingu\Entity\Support\Entity;
use Pingu\Field\Displayers\Options\TrimmedTextOptions;
use Pingu\Field\Support\FieldDisplayerWithOptions;

class TrimmedTextDisplayer extends FieldDisplayerWithOptions
{
    /**
     * @ineritDoc
     */
    public static function friendlyName(): string
    {
        return 'Trimmed';
    }

    /**
     * @ineritDoc
     */
    public static function machineName(): string
    {
        return 'text-trimmed';
    }

    /**
     * @ineritDoc
     */
    public static function optionsClass(): string
    {
        return TrimmedTextOptions::class;
    }

    public function systemView(): string
    {
        return 'field@fields.text-trimmed';
    }

    public function getFieldValue($value)
    {
        return \Str::limit($value, $this->options->value('limit'));
    }
}