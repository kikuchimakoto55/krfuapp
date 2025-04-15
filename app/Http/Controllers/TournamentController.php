<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tournament;

class TournamentController extends Controller
{
    public function store(Request $request)
    {
        // バリデーション（必要に応じて調整）
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:100',
                'categoly' => 'required|integer',
                'year' => 'required|digits:4',
                'event_period_start' => 'required|date',
                'event_period_end' => 'nullable|date',
                'publishing' => 'required|boolean',
                'divisionflg' => 'required|boolean',
                'divisionname' => 'nullable|string',
                'divisionid' => 'nullable|integer',
                'divisions' => 'nullable|json',
            ]);
        } catch (ValidationException $e) {
            Log::error('バリデーション失敗', $e->errors());
            return response()->json(['errors' => $e->errors()], 422);
        }

        // ✅ divisions を JSON 文字列にする
            if (!empty($validated['divisions']) && is_array($validated['divisions'])) {
            $validated['divisions'] = json_encode($validated['divisions'], JSON_UNESCAPED_UNICODE);
        }

        // データ保存
        $tournament = Tournament::create(array_merge(
            $validated,
            [
                'registration_date' => now(),
                'update_date' => now(),
                'del_flg' => 0,
            ]
        ));

        return response()->json(['message' => '大会登録完了', 'tournament' => $tournament]);
    }
}