<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'event_name' => 'required|string|max:100',
            'event_opentime' => 'required|date',
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
            'event_opentime.required' => '開催日時は必須です。',
            'venue_name.required' => '会場名は必須です。',
            'event_name.max' => 'イベント名は100文字以内で入力してください。',
            'event_overview.string' => '概要は文字列で入力してください。',
        ];
    }
}
