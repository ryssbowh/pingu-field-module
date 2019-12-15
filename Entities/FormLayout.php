<?php 

namespace Pingu\Field\Entities;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Pingu\Core\Contracts\HasRouteSlugContract;
use Pingu\Core\Entities\BaseModel;
use Pingu\Core\Traits\Models\HasRouteSlug;
use Pingu\Core\Traits\Models\HasWeight;
use Pingu\Forms\Support\FieldOptions;

class FormLayout extends BaseModel implements HasRouteSlugContract
{
    use HasWeight, HasRouteSlug;

    public $fillable = ['object', 'field', 'weight', 'widget', 'options'];

    public $timestamps = false;

    public $casts = [
        'options' => 'array'
    ];

    /**
     * Options instance
     * @var FieldOptions
     */
    protected $optionsInstance;

    /**
     * @inheritDoc
     */
    public static function boot()
    {
        parent::boot();

        static::saving(
            function ($layout) {
                if (is_null($layout->weight)) {
                    $layout->weight = $layout->getNextWeight(['group_id' => $layout->group_id, 'object' => $layout->object]);
                }
            }
        );

        static::saved(
            function ($layout) {
                \Field::forgetFormLayoutCache($layout->object);
            }
        );
    }

    /**
     * Group relationship
     * @return BelongsTo
     */
    public function group()
    {
        return $this->belongsTo(FormLayoutGroup::class);
    }

    /**
     * Options accessor
     * 
     * @return FieldOptions
     */
    public function getOptionsAttribute(): FieldOptions
    {
        if (is_null($this->optionsInstance)) {
            $class = \FormField::getRegisteredOptions($this->widget);
            $options = json_decode($this->attributes['options'], true);
            $this->optionsInstance = new $class($options, $this->widget);
        }
        return $this->optionsInstance;
    }

    /**
     * Widget accessor
     * 
     * @return string
     */
    public function getWidgetAttribute(): string
    {
        return \FormField::getRegisteredField($this->attributes['widget']);
    }
}