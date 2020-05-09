<?php

namespace Pingu\Field\Support;

use Illuminate\Support\Collection;
use Pingu\Core\Contracts\RouteContexts\ValidatableContextContract;
use Pingu\Field\Contracts\HasFieldsContract;

class ContextValidator extends FieldValidator
{
    /**
     * @var ContextContract
     */
    protected $context;

    public function __construct(HasFieldsContract $object, ValidatableContextContract $context)
    {
        $this->context = $context;
        parent::__construct($object);
    }

    /**
     * Validation messages
     * 
     * @see    https://laravel.com/docs/5.8/validation
     * @return array
     */
    public function getMessages(): array
    {
        return $this->context->getValidationMessages($this->object);
    }

    /**
     * Rules for validation
     * 
     * @see    https://laravel.com/docs/5.8/validation
     * @return array
     */
    public function getRules(): array
    {
        return $this->context->getValidationRules($this->object);
    }
}