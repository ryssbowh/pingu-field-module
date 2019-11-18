<?php

namespace Pingu\Field\Exceptions;

use Pingu\Field\Contracts\HasFields;

class FieldsException extends \Exception
{
    public static function missingRule(string $name, HasFields $model)
    {
        return new static("model ".get_class($model)." : missing a validation rule for '$name'");
    }

    public static function undefined(string $name, $object)
    {
        return new static("Field '$name' not defined in ".get_class($object));
    }

    public static function missingOption($name, $option)
    {
        return new static("Field '$name' is missing a '$option' option'");
    }
}