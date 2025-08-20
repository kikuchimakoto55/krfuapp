<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Illuminate\Validation\Rule;

class StoreCoachRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Sanctumはルートで担保
    }

    public function rules(): array
    {
        return [
            'member_id' => [
            'required',
            'integer',
            Rule::exists('t_members', 'member_id')
                ->where(fn($q) => $q->where('coach_flg', 1)->where('del_flg', 0)),
        ], //「指導員（coach_flg=1）かつ未削除」の会員だけが登録対象

            // 3系統：いずれか1つ以上が必須（後段の withValidator で担保）
            'coach_kind_ids' => ['sometimes', 'array'],
            'coach_kind_ids.*' => ['integer', 'exists:t_coach_kinds,c_categorykinds_id'],

            'committee_kind_ids' => ['sometimes', 'array'],
            'committee_kind_ids.*' => ['integer', 'exists:t_committee_kinds,committeekinds_id'],

            'a_position_kind_ids' => ['sometimes', 'array'],
            'a_position_kind_ids.*' => ['integer', 'exists:t_a_positionkinds,a_positionkinds_id'],

            'remarks'    => ['nullable', 'string'],
            'referee_id' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            $d = $this->all();

            $hasCoach     = !empty($d['coach_kind_ids']) && is_array($d['coach_kind_ids']) && count($d['coach_kind_ids']) > 0;
            $hasCommittee = !empty($d['committee_kind_ids']) && is_array($d['committee_kind_ids']) && count($d['committee_kind_ids']) > 0;
            $hasAPosition = !empty($d['a_position_kind_ids']) && is_array($d['a_position_kind_ids']) && count($d['a_position_kind_ids']) > 0;

            if (!$hasCoach && !$hasCommittee && !$hasAPosition) {
                $v->errors()->add('role', '指導員・委員会・役職のいずれかを最低1つは選択してください。');
            }
        });
    }

    public function messages(): array
    {
        return [
            'member_id.required' => '会員IDは必須です。',
            'member_id.exists'   => '会員IDが存在しない、または無効（削除）です。',

            'coach_kind_ids.array'    => '指導員種別は配列で送信してください。',
            'coach_kind_ids.*.exists' => '存在しない指導員種別が含まれています。',

            'committee_kind_ids.array'    => '委員会種別は配列で送信してください。',
            'committee_kind_ids.*.exists' => '存在しない委員会種別が含まれています。',

            'a_position_kind_ids.array'    => '役職種別は配列で送信してください。',
            'a_position_kind_ids.*.exists' => '存在しない役職種別が含まれています。',
        ];
    }
}
