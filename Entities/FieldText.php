<?php

namespace Pingu\Field\Entities;

use Illuminate\Database\Eloquent\Builder;
use Pingu\Core\Entities\BaseModel;
use Pingu\Entity\Support\Entity;
use Pingu\Field\Displayers\DefaultTextDisplayer;
use Pingu\Field\Displayers\TitleTextDisplayer;
use Pingu\Field\Displayers\TrimmedTextDisplayer;
use Pingu\Forms\Support\Field;
use Pingu\Forms\Support\Fields\TextInput;

class FieldText extends BaseBundleField
{
    protected static $availableWidgets = [TextInput::class];
    
    protected static $availableFilterWidgets = [TextInput::class];

    protected static $displayers = [DefaultTextDisplayer::class, TrimmedTextDisplayer::class, TitleTextDisplayer::class];
    
    protected $fillable = ['default', 'required', 'maxLength'];

    protected $casts = [
        'required' => 'boolean'
    ];

    protected $attributes = [
        'default' => '',
        'maxLength' => 255
    ];

    /**
     * @inheritDoc
     */
    public function defaultValue()
    {
        return $this->default;
    }

    /**
     * @inheritDoc
     */
    public function uncastSingleValue($value)
    {
        return $value;
    }

    /**
     * @inheritDoc
     */
    public function castToSingleFormValue($value)
    {
        return $value;
    }

    /**
     * @inheritDoc
     */
    public function castSingleValue($value)
    {
        return $value;
    }

    /**
     * @inheritDoc
     */
    public function castSingleValueFromDb($value)
    {
        return $value;
    }

    /**
     * @inheritDoc
     */
    public function toSingleDbValue($value)
    {
        return $value;
    }

    /**
     * @inheritDoc
     */
    public function formFieldOptions(int $index = 0): array
    {
        return [
            'required' => $this->required,
            'maxlength' => $this->maxLength
        ];
    }

    /**
     * @inheritDoc
     */
    public function defaultValidationRule(): string
    {
        $rules = 'string'.$this->maxLength ? '|max:'.$this->maxLength : '';
        if ($this->required) {
            $rules .= '|required';
        }
        return $rules;
    }

    /**
     * @inheritDoc
     */
    public function singleFilterQueryModifier(Builder $query, $value, BaseModel $model)
    {
        $query->where('value', 'like', '%'.$value.'%');
    }
}
