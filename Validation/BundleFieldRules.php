<?php

namespace Pingu\Field\Validation;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Eloquent\Builder;
use Pingu\Field\Entities\BundleFieldValue;

class BundleFieldRules
{
    public function unique($attribute, $value, $params)
    {
        extract(array_combine(['class', 'id'], $params));
        $machineName = substr(explode('.', $attribute)[0], 6);
        $query = BundleFieldValue::where('entity_type', $class)
            ->where('value', $value)
            ->whereHas(
                'field', function (Builder $query) use ($machineName) {
                    $query->where('machineName', $machineName);
                }
            );
        if ($id) {
            $query->whereNot('entity_id', $id);
        }

        if ($query->first()) {
            return false;
        }
        return true;
    }
}