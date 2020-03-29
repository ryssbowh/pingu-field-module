<?php

namespace Pingu\Field\Entities;

use Illuminate\Database\Eloquent\SoftDeletes;
use Pingu\Core\Entities\BaseModel;

class BundleFieldValue extends BaseModel
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
        return $this->morphTo('entity');
    }

    public static function forField(BundleField $field)
    {
        return static::where('field_id', $field->id)->get();
    }
}
