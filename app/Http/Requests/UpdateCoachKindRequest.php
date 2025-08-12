<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\NoHtml;

class UpdateCoachKindRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        $id = (int) $this->route('id');

        return [
            'c_categorykindsname' => [
                'required',
                'string',
                'max:100',
                new NoHtml,
                Rule::unique('t_coach_kinds', 'c_categorykindsname')
                    ->ignore($id, 'c_categorykinds_id')
                    ->where(fn($q) => $q->where('del_flg', 0)),
            ],
        ];
    }

    public function messages()
    {
        return [
            'c_categorykindsname.required' => '指導員種別名称は必須です。',
            'c_categorykindsname.unique'   => '同名の指導員種別が既に存在します。',
        ];
    }
}