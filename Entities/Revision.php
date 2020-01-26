<?php

namespace Pingu\Field\Entities;

use Illuminate\Database\Eloquent\Model;
use Pingu\Core\Entities\BaseModel;
use Pingu\Core\Traits\Models\createdBy;

class Revision extends BaseModel
{
    use createdBy;
    
    /**
     * @inheritDoc
     */
    protected $fillable = ['value', 'field', 'revision'];

    /**
     * @inheritDoc
     */
    protected $visible = ['value', 'field', 'revision'];

    /**
     * @inheritDoc
     */
    protected $casts = [
        'value' => 'json'
    ];

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