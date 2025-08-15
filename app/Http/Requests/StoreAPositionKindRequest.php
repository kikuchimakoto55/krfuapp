<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAPositionKindRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'a_positionkindskindsname' => $this->normalize($this->input('a_positionkindskindsname')),
        ]);
    }

    public function rules(): array
    {
        return [
            'a_positionkindskindsname' => [
                'bail',
                'required',
                'string',
                'max:100',
                'NoHtml',
                Rule::unique('t_a_positionkinds', 'a_positionkindskindsname')
                    ->where(fn ($q) => $q->where('del_flg', 0)),
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'a_positionkindskindsname' => '協会担当種別名称',
        ];
    }

    public function messages(): array
    {
        return [
            'a_positionkindskindsname.required' => ':attribute は必須です。',
            'a_positionkindskindsname.max'      => ':attribute は100文字以内で入力してください。',
            'a_positionkindskindsname.NoHtml'   => ':attribute にHTMLタグは使用できません。',
            'a_positionkindskindsname.unique'   => '同名の :attribute が既に存在します（未削除）。',
        ];
    }

    private function normalize(?string $v): ?string
    {
        if ($v === null) return null;
        // NBSP -> space
        $v = str_replace("\xC2\xA0", ' ', $v);
        // 全角スペース -> 半角
        $v = preg_replace('/[　]+/u', ' ', $v);
        // ゼロ幅文字除去
        $v = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $v);
        // 連続空白を1つに
        $v = preg_replace('/\s+/u', ' ', $v);
        return trim($v);
    }
}
