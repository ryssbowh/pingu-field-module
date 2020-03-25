<?php

namespace Pingu\Field\Support\FieldDisplay;

use Illuminate\Support\Collection;
use Pingu\Entity\Contracts\BundleContract;

class FieldDisplayBundle extends FieldDisplay
{
    /**
     * @ingeritDoc
     */
    public function __construct(BundleContract $object)
    {
        parent::__construct($object);
    }

    /**
     * @ingeritDoc
     */
    protected function getObjectAttribute()
    {
        return $this->object->bundleName();
    }

    /**
     * @ingeritDoc
     */
    protected function getFields(): Collection
    {
        return $this->object->fields()->getAll();
    }
}