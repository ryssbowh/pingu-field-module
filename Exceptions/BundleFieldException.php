<?php

namespace Pingu\Field\Exceptions;

use Pingu\Entity\Contracts\BundleContract;
use Pingu\Field\Contracts\BundleFieldContract;
use Pingu\Field\Entities\BundleField;

class BundleFieldException extends \Exception
{

    public static function registered(string $name, BundleFieldContract $field, string $class2)
    {
        return new static("Can't register ".get_class($field)." as bundle : '$name' is already registered by $class2");
    }

    public static function notRegistered(string $name)
    {
        return new static("'$name' is not a registered bundle field");
    }

    public static function notDefined(string $name, BundleContract $bundle)
    {
        return new static("'$name' is not a field defined on bundle '{$bundle->name()}'");
    }

    public static function notTypeDefined(string $name, string $type)
    {
        return new static("'$name' is not a field defined on entity type '$type'");
    }

    public static function alreadyDefined(BundleFieldContract $field, string $name)
    {
        return new static("Cannot add field '$name' from bundle field {$field::friendlyName()}, $name is already defined in ".BundleField::class);
    }

    public static function slugNotSetInRoute()
    {
        return new static("bundle field slug (".BundleField::routeSlug().") must be set in route");
    }

    public static function cantDelete()
    {
        return new static('You can\'t delete this instance, delete its related field instead ($field->field->delete())');
    }

    public static function nameReserved(string $name, BundleFieldContract $field)
    {
        return new static(get_class($field)." can't have a field called $name, this name is reserved by the system.")
    }

}