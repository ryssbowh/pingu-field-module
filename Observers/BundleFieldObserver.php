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
        \FieldLayout::getBundleFormLayout($field->bundle())->createForField($field->instance);
        \FieldDisplay::getBundleDisplay($field->bundle())->createForField($field->instance);
        foreach ($field->bundle()->entities() as $entity) {
            $entity->fieldValues->createDefaultValue($field, $entity);
        }
    }

    public function deleting(BundleField $field)
    {
        \FieldLayout::getBundleFormLayout($field->bundle())->deleteForField($field->instance);
        \FieldDisplay::getBundleDisplay($field->bundle())->deleteForField($field->instance);
        $field->instance->delete();
    }

    public function deleted(BundleField $field)
    {
        \FieldLayout::forgetCache();
        \FieldDisplay::forgetCache();
        \Field::forgetAllFieldCache();
    }
}