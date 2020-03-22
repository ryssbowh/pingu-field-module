<?php

namespace Pingu\Field\Support\FieldRepository;

use Illuminate\Support\Collection;
use Pingu\Entity\Contracts\BundleContract;
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
     * @return ?BundleContract
     */
    protected function getBundle(): ?BundleContract
    {
        return $this->object->bundle();
    }

    /**
     * Returns all bundle fields
     * 
     * @return Collection
     */
    protected function resolveBundleFields(): Collection
    {
        if ($this->getBundle()){
            return $this->getBundle()->fields()->get();
        }
        return collect();
    }

    /**
     * Returns all entity fields
     * 
     * @return Collection
     */
    protected function resolveEntityFields(): Collection
    {
        return parent::resolveFields();
    }

    /**
     * @inheritDoc
     */
    protected function resolveFields(): Collection
    {
        return $this->resolveEntityFields()->merge($this->resolveBundleFields());
    }
}