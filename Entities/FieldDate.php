<?php

namespace Pingu\Field\Entities;

class FieldDate extends FieldDatetime
{
    protected $fillable = ['setToCurrent', 'required'];

    protected $casts = [
        'setToCurrent' => 'boolean',
        'required' => 'boolean'
    ];

    protected $format = "Y-m-d";

    /**
     * @inheritDoc
     */
    public static function friendlyName(): string 
    {
        return 'Date';
    }

}
