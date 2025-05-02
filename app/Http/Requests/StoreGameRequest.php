<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Tournament;

class StoreGameRequest extends FormRequest
{
    public function rules()
    {
        $rules = [
            'tournament_id'   => ['required', 'integer', 'exists:t_tournaments,tournament_id'],
            'division_name'   => ['nullable', 'string'],
            'match_round'     => ['required', 'integer'],
            'match_datetime'  => ['required', 'date'],
            'venue_id'        => ['nullable', 'integer'],
            'team1_id'        => ['nullable', 'integer'],
            'team2_id'        => ['nullable', 'integer'],
            'referee_id'      => ['nullable', 'integer'],
            'staff_id'        => ['nullable', 'integer'],
            'doctor_id'       => ['nullable', 'integer'],
        ];

        // 🔸 条件付きルール（大会が divisionflg = 1 の場合のみ）
        $tournamentId = $this->input('tournament_id');

        if ($tournamentId && Tournament::where('tournament_id', $tournamentId)->value('divisionflg') === 1) {
            $rules['division_name'] = ['required', 'string'];
        }

        return $rules;
    }

    public function authorize()
    {
        return true;
    }

    public function messages()
    {
    return [
        'tournament_id.required' => '大会IDは必須です。',
        'tournament_id.exists'   => '指定された大会が存在しません。',
        'division_name.required' => 'ディビジョン名は必須です（この大会では必須設定です）。',
        'match_round.required'   => '回戦情報を選択してください。',
        'match_datetime.required'=> '開催日時を入力してください。',
        // 他の必要に応じて追加...
    ];
    }
}