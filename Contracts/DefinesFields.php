<?php

namespace Pingu\Field\Contracts;

use Pingu\Field\Contracts\FieldRepository;
use Pingu\Field\Contracts\FieldsValidator;

interface DefinesFields
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