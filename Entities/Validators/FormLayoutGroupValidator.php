<?php

namespace Pingu\Field\Entities\Validators;

use Illuminate\Validation\Validator;
use Pingu\Field\Entities\FormLayoutGroup;
use Pingu\Field\Support\FieldValidator\BaseFieldsValidator;

class FormLayoutGroupValidator extends BaseFieldsValidator
{
    protected function rules(bool $updating): array
    {
        return [
            'name' => 'string|required',
            'object' => 'string|required',
        ];
    }

    protected function messages(): array
    {
        return [

        ];
    }

    protected function modifyValidator(Validator $validator, array $values, bool $updating)
    {
        $validator->after(
            function ($validator) {
                $object = $validator->getData()['object'];
                $name = $validator->getData()['name'];
                $group = FormLayoutGroup::where(['name' => $name, 'object' => $object])->first();
                if ($group) {
                    $validator->errors()->add('name', 'This group already exist');
                }
            }
        );
    }
}