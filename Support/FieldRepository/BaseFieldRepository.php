<?php

namespace Pingu\Field\Support\FieldRepository;

use Illuminate\Support\Collection;
use Illuminate\Support\Traits\ForwardsCalls;
use Pingu\Core\Contracts\HasIdentifierContract;
use Pingu\Field\Contracts\FieldRepositoryContract;
use Pingu\Field\Traits\FieldRepository\HasFields;
use Pingu\Field\Traits\FieldRepository\HasValidationMessages;
use Pingu\Field\Traits\FieldRepository\HasValidationRules;

abstract class BaseFieldRepository implements FieldRepositoryContract
{
    use HasFields, HasValidationMessages, HasValidationRules, ForwardsCalls;
    
    /**
     * @var HasIdentifierContract
     */
    protected $object;
 
    /**
     * Constructor
     * 
     * @param HasIdentifierContract $object
     */
    public function __construct(HasIdentifierContract $object)
    {
        $this->object = $object;
    }

    public function __call($name, $arguments)
    {
        return $this->forwardCallTo($this->resolveFields(), $name, $arguments);
    }

    /**
     * @inheritDoc
     */
    public function validationRules(): Collection
    {
        return $this->resolveRules();
    }

    /**
     * @inheritDoc
     */
    public function validationMessages(): Collection
    {
        return $this->resolveMessages();
    }
}