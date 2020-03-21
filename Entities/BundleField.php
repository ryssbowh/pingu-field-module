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
use Pingu\Entity\Entities\Entity;
use Pingu\Field\Contracts\BundleFieldContract;
use Pingu\Field\Contracts\FieldRepository;
use Pingu\Field\Entities\Fields\BundleFieldFields;
use Pingu\Forms\Support\Field;

class BundleField extends BaseModel implements HasRouteSlugContract
{
    use HasRouteSlug;

    protected $fillable = ['machineName', 'bundle', 'helper', 'name', 'cardinality', 'editable', 'deletable'];

    protected $attributes = [
        'editable' => true,
        'deletable' => true,
        'helper' => ''
    ];

    protected $with = [];

    /**
     * Machinename getter
     * 
     * @return string
     */
    public function getMachineNameAttribute()
    {
        $name = $this->attributes['machineName'];
        return 'field_' . $name;
    }

    /**
     * Creates an instance of BundleField, and an instance of the BundleFieldContract passed in argument
     * 
     * @param array               $values
     * @param BundleContract      $bundle
     * @param BundleFieldContract $bundleField
     * 
     * @return BundleField
     */
    public static function create(array $values, BundleContract $bundle, BundleFieldContract $bundleField): BundleField
    {
        $generic = new static();
        $bundleValues = array_intersect_key($values, array_flip($bundleField->getFillable()));
        $genericValues = array_intersect_key($values, array_flip($generic->getFillable()));
        $bundleField->saveWithRelations($bundleValues);

        if ($fixedCardinality = $bundleField->fixedCardinality() !== false) {
            $genericValues['cardinality'] = $fixedCardinality;
        }
        $generic->bundle = $bundle->bundleName();
        $generic->instance()->associate($bundleField);
        $generic->saveWithRelations($genericValues);
        return $generic;
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
