<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCommitteeKindRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'committeekindsname' => $this->normalize($this->input('committeekindsname')),
        ]);
    }

    public function rules(): array
    {
        $id = (int) $this->route('id'); // /committee-kinds/{id}

        return [
            'committeekindsname' => [
                'bail',
                'required',
                'string',
                'max:100',
                'NoHtml',
                Rule::unique('t_committee_kinds', 'committeekindsname')
                    ->where(fn ($q) => $q->where('del_flg', 0))
                    ->ignore($id, 'committeekinds_id'),
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'committeekindsname' => '委員会種別名称',
        ];
    }

    public function messages(): array
    {
        return [
            'committeekindsname.required' => ':attribute は必須です。',
            'committeekindsname.max'      => ':attribute は100文字以内で入力してください。',
            'committeekindsname.NoHtml'   => ':attribute にHTMLタグは使用できません。',
            'committeekindsname.unique'   => '同名の :attribute が既に存在します（未削除）。',
        ];
    }

    private function normalize(?string $v): ?string
    {
        if ($v === null) return null;
        $v = str_replace("\xC2\xA0", ' ', $v);
        $v = preg_replace('/[　]+/u', ' ', $v);
        $v = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $v);
        $v = preg_replace('/\s+/u', ' ', $v);
        return trim($v);
    }
}