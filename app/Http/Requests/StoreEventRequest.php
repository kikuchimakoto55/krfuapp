<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'event_name' => 'required|string|max:100',
            'event_opentime' => 'nullable|date',
            'weather' => 'nullable|integer',
            'temperature' => 'nullable|integer',
            'venue_name' => 'required|string|max:100',
            'event_kinds' => 'nullable|integer',
            'event_overview' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'event_name.required' => 'イベント名は必須です。',
            'event_opentime.date' => '開催時間は正しい日付形式で入力してください。',
            'venue_id.required' => '会場を入力してください。',
            'event_name.max' => 'イベント名は100文字以内で入力してください。',
            'event_overview.string' => '概要は文字列で入力してください。',
            'venue_id.required' => '会場を入力してください。',
            'venue_id.string' => '会場は文字列で入力してください。',
            'venue_id.max' => '会場名は100文字以内で入力してください。',
        ];
    }
}
