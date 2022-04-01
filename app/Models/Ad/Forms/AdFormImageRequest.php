<?php

namespace App\Models\Ad\Forms;

use Illuminate\Foundation\Http\FormRequest;

class AdFormImageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'image_type'    => 'required|string|max:5',
            'image'         => 'required|string|max:255'
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'image_type.required'  => '此欄位為必填欄位',
            'image_type.max'       => '輸入長度過長',
            'image.required'       => '此欄位為必填欄位',
            'image.max'            => '輸入長度過長'
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
    }

    protected function failedValidation($validator){
        RTErrorsIfExist(200, $validator->errors()); 
    }
}
