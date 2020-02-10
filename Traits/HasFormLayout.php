<?php

namespace Pingu\Field\Traits;

use Pingu\Field\Support\FieldLayout;

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
        \Field::registerFormLayout(get_class($this), new FieldLayout($this));
    }

    /**
     * Get form layout instance from Field facade
     */
    public function formLayout()
    {
        return \Field::getEntityFormLayout($this)->load();
    }
}