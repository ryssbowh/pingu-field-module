<?php

namespace Pingu\Field\Observers;

use Pingu\Field\Entities\BundleField;

class BundleFieldObserver
{
    public function saved(BundleField $field)
    {
        \Field::forgetAllFieldCache();
    }

    public function created(BundleField $field)
    {
        \Field::forgetAllFieldCache();
        \FieldLayout::getFieldLayout($field->bundle()->bundleName())->createForField($field->instance);
        \FieldDisplay::getFieldDisplay($field->bundle()->bundleName())->createForField($field->instance);
        foreach ($field->bundle()->entities() as $entity) {
            $entity->fieldValues->createDefaultValue($field);
        }
    }

    public function deleting(BundleField $field)
    {
        \FieldLayout::getFieldLayout($field->bundle()->bundleName())->deleteForField($field->instance);
        \FieldDisplay::getFieldDisplay($field->bundle()->bundleName())->deleteForField($field->instance);
        $field->instance->delete();
    }

    public function deleted(BundleField $field)
    {
        \FieldLayout::forgetCache($field->bundle()->bundleName());
        \FieldDisplay::forgetCache($field->bundle()->bundleName());
        \Field::forgetAllFieldCache();
    }
}