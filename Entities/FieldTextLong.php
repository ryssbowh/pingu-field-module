<?php

namespace Pingu\Field\Entities;

use Pingu\Forms\Support\Fields\Textarea;

class FieldTextLong extends FieldText
{
    protected static $availableWidgets = [Textarea::class];

    protected $fillable = ['default', 'required', 'maxLength', 'allowHtml'];

    protected $casts = [
        'required' => 'boolean',
        'allowHtml' => 'boolean',
    ];

    protected $attributes = [
        'maxLength' => 16380
    ];

    /**
     * @inheritDoc
     */
    public function toSingleDbValue($value)
    {
        if ($this->allowHtml) {
            return $value;
        }
        return strip_tags($value);
    }

    /**
     * @inheritDoc
     */
    public function formFieldOptions(int $index = 0): array
    {
        return [
            'required' => $this->required,
            'maxlength' => $this->maxLength,
            'allowHtml' => $this->allowHtml
        ];
    }
}
