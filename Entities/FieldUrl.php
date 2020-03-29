<?php

namespace Pingu\Field\Entities;

use Illuminate\Database\Eloquent\Builder;
use Pingu\Core\Entities\BaseModel;
use Pingu\Entity\Support\Entity;
use Pingu\Forms\Support\Field;
use Pingu\Forms\Support\Fields\TextInput;

class FieldUrl extends FieldText
{
   
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
        return 'string'.($this->required ? '|required' : '');
    }
}
