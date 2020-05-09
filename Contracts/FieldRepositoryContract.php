<?php

namespace Pingu\Field\Contracts;

use Illuminate\Support\Collection;
use Pingu\Field\Contracts\FieldContract;

interface FieldRepositoryContract
{
    /**
     * Returns a collection of object's fields
     *
     * @param array $only
     * 
     * @return Collection
     */
    public function get(string $name): FieldContract;

    /**
     * All defined fields
     * 
     * @return Collection
     */
    public function all(): Collection;

    /**
     * Returns the names of all fields
     * 
     * @return array
     */
    public function names(): array;

    /**
     * Validation rules
     * 
     * @return Collection
     */
    public function validationRules(): Collection;

    /**
     * Validation messages
     * 
     * @return Collection
     */
    public function validationMessages(): Collection;
}