<?php

namespace Pingu\Field\Support\FieldRepository;

use Illuminate\Support\Collection;
use Pingu\Entity\Contracts\HasBundleContract;
use Pingu\Field\Entities\BundleField;
use Pingu\Field\Support\FieldRepository\BaseFieldRepository;

/**
 * Defines a Field repository for a bundled entity
 */
abstract class BundledEntityFieldRepository extends BaseFieldRepository
{
    public function __construct(HasBundleContract $object)
    {
        parent::__construct($object);
    }

    /**
     * @inheritDoc
     */
    protected function buildFields(): Collection
    {
        $object = $this->object;
        $bundleFields = $this->object->bundle()->fields()->get();
        $bundleFields->each(
            function ($field) use ($object) {
                $field->setEntity($object);
            }
        );
        return parent::buildFields()->merge($bundleFields);
    }
}