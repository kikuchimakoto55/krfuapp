<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTournamentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // 認可チェックをしない場合は true に
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:100',
            'categoly' => 'required|integer',
            'year' => 'required|digits:4',
            'event_period_start' => 'required|date',
            'event_period_end' => 'nullable|date',
            'publishing' => 'required|boolean',
            'divisionflg' => 'required|boolean',
            'divisionname' => 'nullable|string',
            'divisionid' => 'nullable|integer',

            // ✅ divisionsがある場合は配列としてバリデーション
            'divisions' => 'nullable|array',
            'divisions.*.order' => 'required|integer|min:1',
            'divisions.*.name' => 'required|string|max:50',
        ];
    }
}

