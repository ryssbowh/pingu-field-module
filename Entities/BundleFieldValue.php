<?php

namespace Pingu\Field\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Pingu\Core\Traits\Models\CreatedBy;
use Pingu\Core\Traits\Models\DeletedBy;
use Pingu\Core\Traits\Models\UpdatedBy;

class BundleFieldValue extends Model
{
    use SoftDeletes;

    protected $fillable = ['value', 'revision_id'];

    protected $visible = ['value'];

    protected $dates = ['deleted_at'];

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
