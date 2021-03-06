<?php

namespace Pingu\Field\Entities;

use Pingu\Field\Displayers\DefaultTextDisplayer;
use Pingu\Field\Displayers\TrimmedTextDisplayer;
use Pingu\Forms\Support\Fields\Textarea;

class FieldTextLong extends FieldText
{
    protected static $availableWidgets = [Textarea::class];

    protected static $displayers = [DefaultTextDisplayer::class, TrimmedTextDisplayer::class];

    protected $fillable = ['default', 'required', 'maxLength', 'allowHtml'];

    protected $casts = [
        'required' => 'boolean',
        'allowHtml' => 'boolean',
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
            'allowHtml' => $this->allowHtml
        ];
    }

    /**
     * @inheritDoc
     */
    public function defaultValidationRule(): string
    {
        $rules = 'string'.($this->maxLength ? '|max:'.$this->maxLength : '');
        if ($this->required) {
            $rules .= '|required';
        }
        return $rules;
    }
}
