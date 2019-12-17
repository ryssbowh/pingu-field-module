<?php 

namespace Pingu\Field\Entities;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Pingu\Core\Entities\BaseModel;
use Pingu\Core\Traits\Models\HasWeight;

class FormLayoutGroup extends BaseModel
{
    use HasWeight; 

    public $fillable = ['object', 'name', 'weight'];

    public $timestamps = false;

    protected $with = [];

    public static function boot()
    {
        parent::boot();

        static::saving(
            function ($group) {
                if (is_null($group->weight)) {
                    $group->weight = $group->getNextWeight(['object' => $group->object]);
                }
            }
        );

        static::saved(
            function ($group) {
                \Field::forgetFormLayoutCache($group->object);
            }
        );
    }

    /**
     * Layout (items) relationship
     * 
     * @return HasMany
     */
    public function layout()
    {
        return $this->hasMany(FormLayout::class, 'group_id')->orderBy('weight');
    }

    /**
     * Does this group has a $name field
     * 
     * @param  string  $name
     * 
     * @return boolean
     */
    public function hasField(string $name)
    {
        return !is_null($this->layout()->where('field', $name)->first());
    }

    /**
     * Get a field by its name
     * 
     * @param string $name
     * 
     * @return ?FormLayout
     */
    public function getField(string $name): ?FormLayout
    {
        return $this->layout()->where('field', $name)->first();
    }

    /**
     * Add a FormLayout to this group
     * 
     * @param FormLayout $field
     */
    public function addField(FormLayout $field)
    {
        $this->layout->push($field);
    }
}