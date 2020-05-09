<?php

namespace Pingu\Field\Validation;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Validator;
use Pingu\Field\Entities\BundleFieldValue;

class BundleFieldRules
{
    /**
     * Unique rule for bundle fields
     * 
     * @param string $attribute
     * @param mixed  $value    
     * @param array  $params
     * 
     * @return bool
     */
    public function unique($attribute, $value, $params): bool
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

    /**
     * Rule to check if a bundle field machine name is unique for a bundle
     * 
     * @param string $attribute
     * @param mixed  $value    
     * @param array  $params
     * @param Validator $validator
     * 
     * @return bool
     */
    public function uniqueMachineName($attribute, $value, $params, $validator): bool
    {
        $bundle = request()->route()->parameter('bundle');
        $machineName = 'field_'.$validator->getData()[$attribute];
        $listFields = $bundle->fieldRepository()->keys()->all();
        return !in_array($machineName, $listFields);
    }
}