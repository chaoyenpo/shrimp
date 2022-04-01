<?php

namespace App\Models\ShrimpFarm\Forms;

use Illuminate\Foundation\Http\FormRequest;

class ShrimpFarmEventFormRequest extends FormRequest
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
            'shrimp_farm_id' => ['required','integer','min:1','exists:shrimp_farms,id'],
            'content'        => 'required|string',
            'images'         => '',
            'end_at'         => 'required|string|date|date_format:Y-m-d H:i:s'
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
            'shrimp_farm_id.required' => '此欄位為必填欄位',
            'shrimp_farm_id.integer'  => '此欄位必須為整數',
            'shrimp_farm_id.min'      => '輸入值過小',
            'shrimp_farm_id.exists'   => '找不到對應的釣蝦場',
            'content.required'        => '此欄位為必填欄位',
            'end_at.required'         => '此欄位為必填欄位',
            'end_at.date'             => '此欄位為 Timestamp 欄位',
            'end_at.date_format'      => '格式應為：Y-m-d H:i:s'
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
