<?php

namespace App\Models\ShrimpFarm\Forms;

use Illuminate\Foundation\Http\FormRequest;

class ShrimpFarmFormSearchRequest extends FormRequest
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
            'location_lat' => 'required|numeric|min:-90|max:90',
            'location_lng' => 'required|numeric|min:-180|max:180',
            'type'         => 'required|string'
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
            'location_lat.required' => '此欄位為必填欄位',
            'location_lat.numeric'  => '格式不正確',
            'location_lat.min'      => '輸入值過小',
            'location_lat.max'      => '輸入值過大',
            'location_lng.required' => '此欄位為必填欄位',
            'location_lng.numeric'  => '格式不正確',
            'location_lng.min'      => '輸入值過小',
            'location_lng.max'      => '輸入值過大',
            'type.required'         => '此欄位為必填欄位'
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
            if (!in_array($data['type'], ['all', 'event'])){
                $validator->errors()->add('type', '參數錯誤');
            }
        });
    }

    protected function failedValidation($validator){
        RTErrorsIfExist(200, $validator->errors()); 
    }
}
