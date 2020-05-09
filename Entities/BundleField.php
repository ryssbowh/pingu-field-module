<?php

namespace Pingu\Field\Entities;

use Illuminate\Support\Str;
use Pingu\Content\Events\ContentFieldCreated;
use Pingu\Content\Events\DeletingContentField;
use Pingu\Core\Contracts\HasRouteSlugContract;
use Pingu\Core\Entities\BaseModel;
use Pingu\Core\Traits\Models\HasRouteSlug;
use Pingu\Core\Traits\Models\HasWeight;
use Pingu\Entity\Contracts\BundleContract;
use Pingu\Entity\Support\Entity;
use Pingu\Field\Contracts\BundleFieldContract;
use Pingu\Field\Contracts\FieldRepository;
use Pingu\Field\Entities\Fields\BundleFieldFields;
use Pingu\Field\Http\Contexts\UpdateBundleFieldContext;
use Pingu\Forms\Support\Field;

class BundleField extends BaseModel implements HasRouteSlugContract
{
    use HasRouteSlug;

    protected $fillable = ['machineName', 'bundle', 'helper', 'name', 'cardinality', 'editable', 'deletable'];

    protected $attributes = [
        'editable' => true,
        'deletable' => true
    ];

    public static $routeContexts = [UpdateBundleFieldContext::class];

    /**
     * Creates a new field for a bundle
     * 
     * @param array                 $attributes
     * @param BundleContract|string $bundle
     * @param BundleFieldContract   $field
     * 
     * @return BundleField
     */
    public static function create(array $attributes, $bundle, BundleFieldContract $field): BundleField
    {   
        $field->saveFields($attributes);
        $new = $field->field;
        $new->bundle = $bundle instanceof BundleContract ? $bundle->identifier() : $bundle;
        $new->instance()->associate($field)->save();
        $field->load('field');
        return $new;
    }

    /**
     * Machinename getter
     * 
     * @return string
     */
    public function getMachineNameAttribute()
    {
        return 'field_' . $this->attributes['machineName'];
    }

    /**
     * Morph this field into its instance
     * 
     * @return Relation
     */
    public function instance()
    {
        return $this->morphTo();
    }

    /**
     * Values relation
     * 
     * @return HasMany
     */
    public function values()
    {
        return $this->hasMany(BundleFieldValue::class, 'field_id')->orderBy('revision_id', 'desc');
    }

    /**
     * Bundle getter
     * 
     * @return BundleContract
     */
    public function bundle(): BundleContract
    {
        return \Bundle::get($this->bundle);
    }
}
