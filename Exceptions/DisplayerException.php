<?php

namespace Pingu\Field\Exceptions;

use Pingu\Entity\Support\Entity;

class DisplayerException extends \Exception
{
    public static function notRegistered(string $name)
    {
        return new static("$name is not a registered field displayer");
    }

    public static function noDisplayersForField(string $name)
    {
        return new static("field $name has no registered displayers");
    }
}