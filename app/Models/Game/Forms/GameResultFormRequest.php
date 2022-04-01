<?php

namespace App\Models\Game\Forms;

use Illuminate\Foundation\Http\FormRequest;

class GameResultFormRequest extends FormRequest
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
            'user_id'  => ['required','integer','min:1','exists:users,id'],
            'game_id'  => ['required','integer','min:1','exists:games,id'],
            'level'    => 'required|string',
            'number'   => 'required|numeric|min:1|max:255',
            'point'    => 'required|numeric|min:1|max:255',
            'is_pk'    => 'boolean',
            'result'   => 'required|string',
            'integral' => 'required|numeric|min:1|max:255'
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
            'user_id.required'  => '此欄位為必填欄位',
            'user_id.integer'   => '此欄位必須為整數',
            'user_id.min'       => '輸入值過小',
            'user_id.exists'    => '找不到對應的使用者',
            'game_id.required'  => '此欄位為必填欄位',
            'game_id.integer'   => '此欄位必須為整數',
            'game_id.min'       => '輸入值過小',
            'game_id.exists'    => '找不到對應的比賽',
            'level.required'    => '此欄位為必填欄位',
            'level.string'      => '此欄位必為字串',
            'number.required'   => '此欄位為必填欄位',
            'number.numeric'    => '此欄位必為數字',
            'number.min'        => '輸入值過小',
            'number.max'        => '輸入值過大',
            'point.required'    => '此欄位為必填欄位',
            'point.numeric'     => '此欄位必為數字',
            'point.min'         => '輸入值過小',
            'point.max'         => '輸入值過大',
            'is_pk.boolean'     => '格式不正確',
            'result.required'   => '此欄位為必填欄位',
            'result.string'     => '此欄位必為字串',
            'integral.required' => '此欄位為必填欄位',
            'integral.numeric'  => '此欄位必為數字',
            'integral.min'      => '輸入值過小',
            'integral.max'      => '輸入值過大'
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
