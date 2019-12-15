<?php

namespace Pingu\Field\Contracts;

use Pingu\Field\Contracts\FieldContract;
use Pingu\Field\Contracts\FieldRepository;
use Pingu\Field\Contracts\FieldsValidator;
use Pingu\Field\Support\FieldLayout;

interface HasFields
{
   
    /**
     * Fields repository getter
     * 
     * @return FieldRepository
     */
    public function fields(): FieldRepository;

    /**
     * Field getter
     * 
     * @param string $name
     * 
     * @return FieldContract
     */
    public function getField(string $name): FieldContract;

    /**
     * FieldsValidator getter
     * 
     * @return FieldsValidator
     */
    public function validator(): FieldsValidator;
}