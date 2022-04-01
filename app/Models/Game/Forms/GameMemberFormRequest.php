<?php

namespace App\Models\Game\Forms;

use Illuminate\Foundation\Http\FormRequest;

class GameMemberFormRequest extends FormRequest
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
            'user_id'     => ['required','integer','min:1','exists:users,id'],
            'game_id'     => ['required','integer','min:1','exists:games,id'],
            'status'      => 'required|string',
            'register_at' => 'required|string|date|date_format:Y-m-d H:i:s',
            'is_check_in' => 'boolean'
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
            'user_id.required'        => '此欄位為必填欄位',
            'user_id.integer'         => '此欄位必須為整數',
            'user_id.min'             => '輸入值過小',
            'user_id.exists'          => '找不到對應的使用者',
            'game_id.required'        => '此欄位為必填欄位',
            'game_id.integer'         => '此欄位必須為整數',
            'game_id.min'             => '輸入值過小',
            'game_id.exists'          => '找不到對應的比賽',
            'status.required'         => '此欄位為必填欄位',
            'status.string'           => '此欄位必為字串',
            'register_at.required'    => '此欄位為必填欄位',
            'register_at.string'      => '此欄位必為字串',
            'register_at.date'        => '此欄位為 Timestamp 欄位',
            'register_at.date_format' => '格式應為：Y-m-d H:i:s',
            'is_check_in.boolean'     => '格式不正確'
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
