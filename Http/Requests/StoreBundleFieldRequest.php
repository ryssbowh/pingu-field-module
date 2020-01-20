<?php

namespace Pingu\Field\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Pingu\Field\Entities\BundleField;

class StoreBundleFieldRequest extends FormRequest
{
    public function getField()
    {
        $field = $this->post('_field');
        $field = \Field::getRegisteredBundleField($field);
        return new $field;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return $this->getField()->validator()->getRules();
    }

    public function messages()
    {
        return $this->getField()->validator()->getMessages();
    }

    /**
     * Get the validated data from the request.
     *
     * @return array
     */
    public function validated()
    {
        return $this->getField()->validator()->castValues($this->validator->validated());
    }

    /**
     * Configure the validator instance.
     *
     * @param \Illuminate\Validation\Validator $validator
     * 
     * @return void
     */
    public function withValidator($validator)
    {
        $bundle = $this->route()->parameter('bundle');
        $validator->after(
            function ($validator) use ($bundle) {
                $machineName = 'field_'.$validator->getData()['machineName'];
                $listFields = $bundle->fields()->allNames();
                if (in_array($machineName, $listFields)) {
                    $validator->errors()->add('machineName', 'This machine name already exists for this bundle');
                }
            }
        );
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}
