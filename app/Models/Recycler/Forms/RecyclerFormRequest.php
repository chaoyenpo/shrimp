<?php

namespace App\Models\Recycler\Forms;

use Illuminate\Foundation\Http\FormRequest;

class RecyclerFormRequest extends FormRequest
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
            'phone'         => 'required|string|max:20',
            'weight'        => 'required|numeric'
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
            'phone.required'      => '此欄位為必填欄位',
            'phone.string'        => '此欄位必須為字串',
            'phone.max'           => '此欄位長度過長',
            'weight.required'     => '此欄位為必填欄位',
            'weight.numeric'      => '此欄位必須為浮點數',
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
