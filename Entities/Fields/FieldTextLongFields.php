<?php

namespace Pingu\Field\Entities\Fields;

use Illuminate\Support\Collection;
use Pingu\Field\BaseFields\Boolean;
use Pingu\Field\BaseFields\Integer;
use Pingu\Field\BaseFields\LongText;
use Pingu\Field\Support\FieldRepository\BundleFieldFieldRepository;

class FieldTextLongFields extends BundleFieldFieldRepository
{
    /**
     * @inheritDoc
     */
    protected function fields(): array
    {
        return [
            new LongText('default'),
            new Boolean('required'),
            new Boolean('allowHtml'),
            new Integer(
                'maxLength',
                [
                    'label' => 'Maximum length',
                    'max' => 16380
                ]
            )
        ];
    }

    protected function alterFieldsForForm(Collection $fields, bool $updating)
    {
        if ($updating) {
            $fields['maxLength']->option('disabled', true);
        }
    }
}