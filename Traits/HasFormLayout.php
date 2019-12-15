<?php

namespace Pingu\Field\Traits;

use Pingu\Field\Support\FieldLayoutBundled;

trait HasFormLayout
{
    public static function bootHasFormLayout()
    {
        static::registered(
            function ($entity) {
                $entity->registerFormLayout();
            }
        );
    }

    /**
     * Register form layout instance in Field facade
     */
    public function registerFormLayout()
    {
        \Field::registerFormLayout(get_class($this), new FieldLayoutBundled($this));
    }

    /**
     * Get form layout instance from Field facade
     */
    public static function formLayout()
    {
        return \Field::getFormLayout(static::class);
    }
}