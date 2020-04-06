<?php

namespace Pingu\Field\Entities;

use Pingu\Field\Displayers\DefaultUrlDisplayer;

class FieldUrl extends FieldText
{
    protected static $displayers = [DefaultUrlDisplayer::class];

    protected $fillable = ['required', 'default'];

    protected $attributes = [
        'default' => ''
    ];

    /**
     * @inheritDoc
     */
    public function formFieldOptions(int $index = 0): array
    {
        return [
            'required' => $this->required
        ];
    }

    /**
     * @inheritDoc
     */
    public function defaultValidationRule(): string
    {
        return 'string|valid_url'.($this->required ? '|required' : '');
    }
}
