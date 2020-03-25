<?php 

namespace Pingu\Field\Entities;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Pingu\Core\Contracts\HasRouteSlugContract;
use Pingu\Core\Entities\BaseModel;
use Pingu\Core\Traits\Models\HasRouteSlug;
use Pingu\Core\Traits\Models\HasWeight;
use Pingu\Entity\Entities\ViewMode;
use Pingu\Field\Contracts\FieldDisplayerContract;

class DisplayField extends BaseModel implements HasRouteSlugContract
{
    use HasWeight, HasRouteSlug;

    public $fillable = ['object', 'field', 'weight', 'hidden', 'options', 'displayer'];

    public $timestamps = false;

    public $casts = [
        'options' => 'array'
    ];

    public $with = ['view_mode'];

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
            function ($display) {
                if (is_null($display->weight)) {
                    $display->weight = $display->getNextWeight(['object' => $display->object]);
                }
            }
        );

        static::saved(
            function ($display) {
                \FieldDisplay::forgetDisplayCache($display->object);
            }
        );

        static::deleted(
            function ($display) {
                \FieldDisplay::forgetDisplayCache($display->object);
            }
        );
    }

    /**
     * Displayer accessor
     * 
     * @return FieldDisplayerContract
     */
    public function getDisplayerAttribute(): FieldDisplayerContract
    {
        $class = \FieldDisplay::getRegisteredDisplayer($this->attributes['displayer']);
        return new $class($this->options);
    }

    /**
     * View mode relationship
     * 
     * @return BelongsTo
     */
    public function view_mode()
    {
        return $this->belongsTo(ViewMode::class);
    }
}