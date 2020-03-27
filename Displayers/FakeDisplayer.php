<?php 

namespace Pingu\Field\Displayers;

use Pingu\Field\Support\FieldDisplayer;

class FakeDisplayer extends FieldDisplayer
{
    public static function machineName(): string
    {
        return 'none';
    }

    public static function friendlyName(): string
    {
        return 'None';
    }

    public static function hasOptions(): bool
    {
        return false;
    }

    public static function optionsClass(): string
    {
        return '';
    }
}