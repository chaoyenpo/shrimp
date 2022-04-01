<?php

namespace App\Models\Ad\Forms;

use Illuminate\Foundation\Http\FormRequest;

class AdFormSearchRequest extends FormRequest
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
            'location_lat'  => 'numeric',
            'location_lng'  => 'numeric',
            'type'          => 'required',
            'category'      => 'required|integer|min:0',
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
            'location_lat.numeric' => '格式不正確',
            'location_lng.numeric' => '格式不正確',
            'type.required'        => '此欄位為必填欄位',
            'category.required'    => '此欄位為必填欄位',
            'category.integer'     => '此欄位必須為整數',
            'category.min'         => '輸入值過小',
            'category.max'         => '輸入值過大',
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
        $validator->after(function ($validator) {
        	$data = $validator->getData();
            if (!in_array($data['type'], ['all', 'self'])){
                $validator->errors()->add('type', '參數錯誤');
            }
        });
    }

    protected function failedValidation($validator){
        RTErrorsIfExist(200, $validator->errors()); 
    }
}
