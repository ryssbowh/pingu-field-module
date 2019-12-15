<?php 

namespace Pingu\Field\Entities;

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
                if (!$group->weight) {
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

    public function layout()
    {
        return $this->hasMany(FormLayout::class, 'group_id')->orderBy('weight');
    }

    public function hasField(string $name)
    {
        return !is_null($this->layout()->where('field', $name)->first());
    }

    public function addField(FormLayout $field)
    {
        $this->layout->push($field);
    }
}