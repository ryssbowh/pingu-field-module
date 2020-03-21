<?php

namespace Pingu\Field\Observers;

use Pingu\Field\Entities\BundleField;

class BundleFieldObserver
{
    public function saved(BundleField $field)
    {
        \Field::forgetAllFieldCache();
        \Field::forgetFormLayoutCache($field->bundle);
    }

    public function created(BundleField $field)
    {
        \Field::getBundleFormLayout($field->bundle())->createForField($field->instance);
        foreach ($field->bundle()->entities() as $entity) {
            $entity->fieldValues->createDefaultValue($field, $entity);
        }
    }

    public function deleting(BundleField $field)
    {
        \Field::getBundleFormLayout($field->bundle())->deleteForField($field->instance);
    }

    public function deleted(BundleField $field)
    {
        \Field::forgetAllFieldCache();
        \Field::forgetFormLayoutCache($field->bundle);
    }
}