<?php

namespace App\Models\Illustration\Forms;

use Illuminate\Foundation\Http\FormRequest;

class IllustrationFormRequest extends FormRequest
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
            'name'          => 'required|string',
            'steps'         => 'required|numeric|min:1|max:255',
            'lengths'       => 'required|numeric|min:1|max:10',
            'photo1'        => 'nullable|url',
            'photo2'        => 'nullable|url',
            'reviews'       => 'nullable|string',
            'price'         => 'nullable|string',
            'manufacturer'  => 'nullable|string',
            'brand'         => 'nullable|string',
            'youtube'       => 'nullable|url'
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
            'name.required'       => '此欄位為必填欄位',
            'name.string'         => '此欄位必為字串',
            'steps.required'      => '此欄位為必填欄位',
            'steps.numeric'       => '此欄位必為數字',
            'steps.min'           => '輸入值過小',
            'steps.max'           => '輸入值過大',
            'lengths.required'    => '此欄位為必填欄位',
            'lengths.numeric'     => '此欄位必為數字',
            'lengths.min'         => '輸入值過小',
            'lengths.max'         => '輸入值過大',
            'photo1.url'          => '格式不正確',
            'photo2.url'          => '格式不正確',
            'reviews.string'      => '此欄位必為字串',
            'price.string'        => '此欄位必為字串',
            'manufacturer.string' => '此欄位必為字串',
            'brand.string'        => '此欄位必為字串',
            'youtube'             => '格式不正確'
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
