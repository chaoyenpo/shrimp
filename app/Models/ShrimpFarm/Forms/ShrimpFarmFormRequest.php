<?php

namespace App\Models\ShrimpFarm\Forms;

use Illuminate\Foundation\Http\FormRequest;

class ShrimpFarmFormRequest extends FormRequest
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
            'name'         => 'required|string|max:255',
            'address'      => 'required|string|max:255',
            'location_lat' => 'numeric|min:-90|max:90',
            'location_lng' => 'numeric|min:-180|max:180',
            'phone'        => 'required|string|max:20',
            'content'      => '',
            'news'         => '',
            'can_push'     => 'boolean',
            'is_close'     => 'boolean',
            'day'          => 'array',
            'begin_at'     => 'array',
            'end_at'       => 'array'
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
            'name.required'        => '此欄位為必填欄位',
            'name.max'             => '輸入長度過長',
            'address.required'     => '此欄位為必填欄位',
            'address.max'          => '輸入長度過長',
            'location_lat.numeric' => '格式不正確',
            'location_lat.min'     => '輸入值過小',
            'location_lat.max'     => '輸入值過大',
            'location_lng.numeric' => '格式不正確',
            'location_lng.min'     => '輸入值過小',
            'location_lng.max'     => '輸入值過大',
            'phone.required'       => '此欄位為必填欄位',
            'phone.max'            => '輸入長度過長',
            'can_push.boolean'     => '格式不正確',
            'is_close.boolean'     => '格式不正確'
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
