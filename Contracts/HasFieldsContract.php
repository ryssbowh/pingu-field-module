<?php

namespace Pingu\Field\Contracts;

use Illuminate\Support\Collection;
use Pingu\Core\Contracts\HasIdentifierContract;
use Pingu\Field\Contracts\FieldRepositoryContract;
use Pingu\Field\Contracts\HasFieldContextContract;

interface HasFieldsContract extends HasIdentifierContract
{
    /**
     * Fields repository getter
     * 
     * @return FieldRepositoryContract
     */
    public function fieldRepository(): FieldRepositoryContract;

    /**
     * Field names that can be filtered on
     * 
     * @return array
     */
    public function getFilterable(): array;

    /**
     * Fields getter
     * 
     * @return Collection
     */
    public function getFields(): Collection;
}