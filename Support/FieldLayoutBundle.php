<?php 

namespace Pingu\Field\Support;

use Illuminate\Support\Collection;
use Pingu\Entity\Contracts\BundleContract;

class FieldLayoutBundle extends FieldLayout
{
    /**
     * Constructor
     * 
     * @param HasFields $object
     */
    public function __construct(BundleContract $object)
    {
        parent::__construct($object);
    }

    /**
     * Which string is to be saved in the 'object' field of FormLayoutGroup and FormLayout
     * 
     * @return string
     */
    protected function getObjectAttribute()
    {
        return $this->object->bundleName();
    }

    /**
     * @return Collection
     */
    protected function getFields(): Collection
    {
        return $this->object->fields()->getAll();
    }
}