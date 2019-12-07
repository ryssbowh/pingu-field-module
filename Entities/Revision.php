<?php

namespace Pingu\Field\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Pingu\Core\Entities\BaseModel;
use Pingu\Core\Traits\Models\createdBy;

class Revision extends BaseModel
{
    use createdBy;
    
    protected $fillable = ['value', 'field', 'revision'];

    protected $visible = ['value', 'field', 'revision'];

    /**
     * Morph to an entity
     * 
     * @return MorphTo
     */
    public function entity()
    {
        return $this->morphTo();
    }
}