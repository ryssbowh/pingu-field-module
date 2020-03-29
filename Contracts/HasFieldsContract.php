<?php

namespace Pingu\Field\Contracts;

interface HasFieldsContract
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