<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTournamentResultRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // 必要に応じて認可処理を追加
    }

    public function rules(): array
{
    return [
        'tournament_id' => 'required|integer|exists:t_tournaments,tournament_id',
        'results' => 'required|array|min:1',
        'results.*.division_order' => 'required|integer',
        'results.*.division_name' => 'required|string|max:100',
        'results.*.rank_order' => 'nullable|integer',
        'results.*.rank_label' => 'nullable|string|max:50',
        'results.*.team_id' => 'nullable|integer|exists:t_teams,id',
        'results.*.report' => 'nullable|string',
        'results.*.document' => 'nullable|file|max:20480',
    ];
}



    public function messages(): array
    {
        return [
            'results.*.division_order.required' => 'ディビジョン順序は必須です',
            'results.*.division_name.required' => 'ディビジョン名は必須です',
            'results.*.team_id.integer' => 'チームIDは数値で入力してください',
        ];
    }
}