<?php

namespace Pingu\Field\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BundleFieldValue extends Model
{
    use SoftDeletes;

    protected $fillable = ['value'];

    protected $visible = ['value'];
    
    /**
     * Field relation
     * 
     * @return Relation
     */
    public function field()
    {
        return $this->belongsTo(BundleField::class);
    }

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
