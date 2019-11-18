<?php

namespace Pingu\Field\Contracts;

use Pingu\Core\Entities\BaseModel;
use Pingu\Entity\Contracts\BundleContract;

interface BundleFieldContract extends FieldContract, HasFields
{   
    /**
     * Unique name for that field
     *
     * @return string
     */
    public static function uniqueName(): string;
}