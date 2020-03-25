<?php

namespace Pingu\Field\Entities\Fields;

use Pingu\Field\BaseFields\Boolean;
use Pingu\Field\BaseFields\_List;
use Pingu\Field\Support\FieldRepository\BundleFieldFieldRepository;

class FieldEntityFields extends BundleFieldFieldRepository
{
    /**
     * @inheritDoc
     */
    protected function fields(): array
    {
        return [
            new _List(
                'entity',
                [
                    'items' => $this->getEntities()
                ]
            ),
            new Boolean('required')
        ];
    }

    protected function getEntities()
    {
        $out = [];
        foreach (\Entity::getRegisteredEntities() as $entity) {
            $out[$entity] = $entity::friendlyName();
        }
        ksort($out);
        return $out;
    }
}