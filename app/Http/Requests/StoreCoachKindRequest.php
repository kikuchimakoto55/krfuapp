<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\NoHtml;

class StoreCoachKindRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'c_categorykindsname' => [
                'required',
                'string',
                'max:100',
                new NoHtml,
                // del_flg=0 の中で一意
                Rule::unique('t_coach_kinds', 'c_categorykindsname')
                    ->where(fn($q) => $q->where('del_flg', 0)),
            ],
        ];
    }

    public function messages()
    {
        return [
            'c_categorykindsname.required' => '指導員種別名称は必須です。',
            'c_categorykindsname.string'   => '指導員種別名称は文字列で入力してください。',
            'c_categorykindsname.max'      => '指導員種別名称は100文字以内で入力してください。',
            'c_categorykindsname.unique'   => '同名の指導員種別が既に存在します。',
        ];
    }

    protected function prepareForValidation(): void
    {
        $name = $this->input('c_categorykindsname');
        if (is_string($name)) {
            $v = str_replace(["\u{00A0}", "\u{3000}"], ' ', $name);     // NBSP/全角→半角スペース
            $v = preg_replace('/\s+/u', ' ', $v);                        // 連続空白を1つへ
            $v = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $v); // zero-width削除
            $v = trim($v);
            $this->merge(['c_categorykindsname' => $v]);
        }
    }
}