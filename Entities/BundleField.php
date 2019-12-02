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
use Pingu\Forms\Support\FormRepository;

class BundleField extends BaseModel implements HasRouteSlugContract
{
    use HasWeight, HasRouteSlug;

    protected $fillable = ['weight', 'machineName', 'bundle', 'helper', 'name', 'cardinality'];

    protected $attributes = [
        'editable' => true,
        'deletable' => true,
        'helper' => ''
    ];

    protected $with = [];

    public static function boot()
    {
        parent::boot();

        static::saving(
            function ($field) {
                if (is_null($field->weight)) {
                    $field->weight = $field::getNextWeight(['bundle' => $field->bundle]);
                }
            }
        );

        static::deleted(
            function($field) {
                \Field::forgetAllFieldCache();
            }
        );

        static::saved(
            function($field) {
                \Field::forgetAllFieldCache();
            }
        );
    }

    /**
     * Machinename getter
     * 
     * @return string
     */
    public function getMachineNameAttribute()
    {
        $name = $this->attributes['machineName'];
        if (!Str::startsWith($name, 'field_')) {
            $name = 'field_' . $name;
        }
        return $name;
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
     * Get this field latest value for an entity
     * 
     * @param Entity $entity
     * 
     * @return BundleFieldValue|null
     */
    public function getLatestValue(Entity $entity)
    {
        return $this->values
            ->where('entity_id', $entity->id)
            ->where('entity_type', get_class($entity))
            ->first();
    }

    /**
     * Value for that field for an entity
     * 
     * @param  Entity   $entity
     * @param  int|null $revision
     * @return mixed
     */
    public function value(Entity $entity)
    {
        $value = $this->getLatestValue($entity);
        if ($value) {
            return $value->value;
        }
        return null;
    }

    /**
     * Bundle getter
     * 
     * @return BundleContract
     */
    public function bundle(): BundleContract
    {
        return \Bundle::getBundle($this->bundle);
    }
}
