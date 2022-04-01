<?php

namespace App\Models\Ad\Forms;

use Illuminate\Foundation\Http\FormRequest;

class AdFormRequest extends FormRequest
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
            'type'          => 'required',
            'category'      => 'required|integer|min:0',
            'name'          => 'required|string|max:255',
            'url'           => 'required|url',
            'image_type'    => 'required|string|max:5',
            'image'         => 'required|string|max:255',
            'height'        => 'required|integer|min:0|max:5',
            'weight'        => 'required|integer|min:0|max:50',
            'location_lat'  => 'numeric',
            'location_lng'  => 'numeric',
            'shopee'        => 'nullable|url',
            'fb_group'      => 'nullable|url',
            'fb_page'       => 'nullable|url',
            'ig'            => 'nullable|url',
            'youtube'       => 'nullable|url',
            'sales_farm'    => 'array',
            'sales_shop'    => 'array',
            'is_enabled'    => 'boolean'
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
            'type.required'        => '此欄位為必填欄位',
            'category.required'    => '此欄位為必填欄位',
            'category.integer'     => '此欄位必須為整數',
            'category.min'         => '輸入值過小',
            'category.max'         => '輸入值過大',
            'name.required'        => '此欄位為必填欄位',
            'name.max'             => '輸入長度過長',
            'url.required'         => '此欄位為必填欄位',
            'url.url'              => '格式不正確',
            'image_type.required'  => '此欄位為必填欄位',
            'image_type.max'       => '輸入長度過長',
            'image.required'       => '此欄位為必填欄位',
            'image.max'            => '輸入長度過長',
            'height.required'      => '此欄位為必填欄位',
            'height.integer'       => '此欄位必須為整數',
            'height.min'           => '輸入值過小',
            'height.max'           => '輸入值過大',
            'weight.required'      => '此欄位為必填欄位',
            'weight.integer'       => '此欄位必須為整數',
            'weight.min'           => '輸入值過小',
            'weight.max'           => '輸入值過大',
            'location_lat.numeric' => '格式不正確',
            'location_lng.numeric' => '格式不正確',
            'shopee.url'           => '格式不正確',
            'fb_group.url'         => '格式不正確',
            'fb_page.url'          => '格式不正確',
            'ig.url'               => '格式不正確',
            'youtube.url'          => '格式不正確',
            'sales_farm.array'     => '格式不正確',
            'sales_shop.array'     => '格式不正確',
            'is_enabled.boolean'   => '格式不正確'
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
            if (!in_array($data['type'], ['preview', 'confirm'])){
                $validator->errors()->add('type', '參數錯誤');
            }
        });
    }

    protected function failedValidation($validator){
        RTErrorsIfExist(200, $validator->errors()); 
    }
}
