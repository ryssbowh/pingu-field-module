<?php

namespace Pingu\Field\Entities\Fields;

use Pingu\Field\BaseFields\Boolean;
use Pingu\Field\BaseFields\Email;
use Pingu\Field\Support\FieldRepository\BundleFieldFieldRepository;

class FieldEmailFields extends BundleFieldFieldRepository
{
    /**
     * @inheritDoc
     */
    protected function fields(): array
    {
        return [
            new Email('default'),
            new Boolean('required')
        ];
    }

    /**
     * @inheritDoc
     */
    protected function rules(): array
    {
        return [
            'default' => 'email',
            'required' => 'boolean'
        ];
    }

    /**
     * @inheritDoc
     */
    protected function messages(): array
    {
        return [
            'title.required' => 'Title is required'
        ];
    }
}