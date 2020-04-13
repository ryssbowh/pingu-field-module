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
        $bundle = $field->bundle();
        \Field::forgetAllFieldCache();
        \FieldLayout::getFieldLayout($bundle)->createForField($field->instance);
        \FieldDisplay::getFieldDisplay($bundle)->createForField($field->instance);
        foreach ($bundle->entities() as $entity) {
            $entity->fieldValues->createDefaultValue($field);
        }
    }

    public function deleting(BundleField $field)
    {
        $bundle = $field->bundle();
        \FieldLayout::getFieldLayout($bundle)->deleteForField($field->instance);
        \FieldDisplay::getFieldDisplay($bundle)->deleteForField($field->instance);
        $field->instance->delete(true);
    }

    public function deleted(BundleField $field)
    {
        $bundle = $field->bundle();
        \FieldLayout::forgetCache($bundle);
        \FieldDisplay::forgetCache($bundle);
        \Field::forgetAllFieldCache();
    }
}