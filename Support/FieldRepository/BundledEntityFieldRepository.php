<?php

namespace Pingu\Field\Support\FieldRepository;

use Illuminate\Support\Collection;
use Pingu\Entity\Contracts\BundleContract;
use Pingu\Field\Exceptions\FieldRepositoryException;
use Pingu\Field\Support\FieldRepository\BaseFieldRepository;

/**
 * Defines a Field repository for a bundled entity
 */
abstract class BundledEntityFieldRepository extends BaseFieldRepository
{
    /**
     * Get the bundle assocuated to the entity
     *
     * @throws FieldRepositoryException
     * 
     * @return BundleContract
     */
    protected function getBundle(): BundleContract
    {
        if (!$this->object->bundle()) {
            throw FieldRepositoryException::bundleNotSet($this->object);
        }
        return $this->object->bundle();
    }

    /**
     * Returns all bundle fields
     * 
     * @return Collection
     */
    protected function resolveBundleFields(): Collection
    {
        return $this->getBundle()->fields()->get();
    }

    /**
     * @inheritDoc
     */
    protected function resolveFields(): Collection
    {
        return parent::resolveFields()->merge($this->resolveBundleFields());
    }
}