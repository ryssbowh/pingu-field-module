<?php

namespace Pingu\Field\Support\FieldRepository;

use Illuminate\Support\Collection;
use Pingu\Entity\Contracts\BundleContract;
use Pingu\Entity\Contracts\HasBundleContract;
use Pingu\Entity\Exceptions\EntityException;
use Pingu\Field\Entities\BundleField;
use Pingu\Field\Support\FieldRepository\BaseFieldRepository;

/**
 * Defines a Field repository for a bundled entity
 */
abstract class BundledEntityFieldRepository extends BaseFieldRepository
{
    protected $bundle;
    protected $bundleFieldsAdded = false;

    public function __construct(HasBundleContract $object)
    {
        parent::__construct($object);
        if ($bundle = $object->bundle()) {
            $this->bundle = $bundle;
        }
    }

    public function setBundle(BundleContract $bundle)
    {
        $this->bundle = $bundle;
        $this->resolveFields();
        if (!$this->bundleFieldsAdded) {
            $this->fields = $this->fields->merge($this->getBundleFields($bundle));
            $this->bundleFieldsAdded = true;
        }
    }

    protected function getBundleFields(BundleContract $bundle)
    {
        $object = $this->object;
        $bundleFields = $bundle->fields()->get();
        $bundleFields->each(
            function ($field) use ($object) {
                $field->setEntity($object);
            }
        );
        return $bundleFields;
    }

    /**
     * @inheritDoc
     */
    protected function buildFields(): Collection
    {
        $object = $this->object;
        $fields = parent::buildFields();
        if ($bundle = $this->object->bundle()) {
            $this->bundleFieldsAdded = true;
            return $fields->merge($this->getBundleFields($bundle));
        }
        return $fields;
    }
}