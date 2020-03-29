<?php

namespace Pingu\Field\Contracts;

use Pingu\Core\Contracts\HasIdentifierContract;

interface HasFieldsContract extends HasIdentifierContract
{
    /**
     * Fields repository getter
     * 
     * @return FieldRepository
     */
    public function fields(): FieldRepository;

    /**
     * FieldsValidator getter
     * 
     * @return FieldsValidator
     */
    public function validator(): FieldsValidator;
}