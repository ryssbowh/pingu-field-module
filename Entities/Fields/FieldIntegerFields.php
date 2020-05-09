<?php

namespace Pingu\Field\Entities\Fields;

use Pingu\Field\BaseFields\Boolean;
use Pingu\Field\BaseFields\Integer;
use Pingu\Field\Support\FieldRepository\BundleFieldFieldRepository;

class FieldIntegerFields extends BundleFieldFieldRepository
{
    /**
     * @inheritDoc
     */
    protected function fields(): array
    {
        return [
            new Integer('default'),
            new Boolean('required')
        ];
    }

    /**
     * @inheritDoc
     */
    protected function rules(bool $updating): array
    {
        return [
            'default' => 'string',
            'required' => 'boolean'
        ];
    }

    /**
     * @inheritDoc
     */
    protected function messages(): array
    {
        return [];
    }
}