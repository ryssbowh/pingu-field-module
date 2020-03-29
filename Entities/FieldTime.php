<?php

namespace Pingu\Field\Entities;

use Illuminate\Database\Eloquent\Builder;
use Pingu\Core\Entities\BaseModel;
use Pingu\Entity\Entities\Entity;

class FieldTime extends FieldDatetime
{
    protected $defaultFormat = "H:i:s";
    protected $dbFormat = 'H:i:s';

    /**
     * @inheritDoc
     */
    public static function friendlyName(): string 
    {
        return 'Time';
    }

    /**
     * @inheritDoc
     */
    public function singleFilterQueryModifier(Builder $query, $value, BaseModel $model)
    {
        $query->where('value', '=', $value);
    }
}