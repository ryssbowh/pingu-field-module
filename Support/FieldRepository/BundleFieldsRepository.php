<?php

namespace Pingu\Field\Support\FieldRepository;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Pingu\Field\Contracts\FieldRepository;
use Pingu\Field\Entities\BundleField;

/**
 * Defines a list of fields for a bundle
 */
class BundleFieldsRepository extends FieldRepository
{
    /**
     * @inheritDoc
     */
    protected function buildFields(): Collection
    {
        if (!Schema::hasTable('bundle_fields')) {
            return collect();
        }

        $fields = [];
        $bundleFields = BundleField::where(['bundle' => $this->object->bundleName()])->orderBy('name')->get();
        foreach ($bundleFields as $field) {
            $fields[$field->machineName] = $field->instance;
        }
        return collect($fields);
    }
}