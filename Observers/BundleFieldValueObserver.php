<?php

namespace Pingu\Field\Observers;

use Pingu\Field\Entities\BundleFieldValue;

class BundleFieldValueObserver
{
    public function saved(BundleFieldValue $value)
    {
        \Field::forgetBundleValuesCache($value->entity);
    }

    public function deleting(BundleFieldValue $value)
    {
        \Field::forgetBundleValuesCache($value->entity);
    }
}