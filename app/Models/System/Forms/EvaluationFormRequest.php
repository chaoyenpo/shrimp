<?php

namespace App\Models\System\Forms;

use Illuminate\Foundation\Http\FormRequest;

class EvaluationFormRequest extends FormRequest
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
            'score'          => 'required|integer|min:1|max:5',
            'description'    => 'required|string|max:255'
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
            'score.required'          => '此欄位為必填欄位',
            'score.integer'           => '此欄位必須為整數',
            'score.min'               => '輸入值過小',
            'score.max'               => '輸入值過大',
            'description.required'    => '此欄位為必填欄位',
            'description.max'         => '輸入長度過長'
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
