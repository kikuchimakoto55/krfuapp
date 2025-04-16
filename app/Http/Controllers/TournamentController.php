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

    // tournaments テーブルの一覧を取得
    public function index()
    {
    $tournaments = Tournament::orderBy('event_period_start', 'desc')->get();
    return response()->json($tournaments);
    }

    // tournaments編集処理
    public function show($id)
    {
    $tournament = Tournament::findOrFail($id);
    return response()->json($tournament);
    }

    public function update(Request $request, $id)
    {
    $validated = $request->validate([
        'name' => 'required|string|max:100',
        'categoly' => 'required|integer',
        'year' => 'required|digits:4',
        'event_period_start' => 'required|date',
        'event_period_end' => 'nullable|date',
        'publishing' => 'required|boolean',
        'divisionflg' => 'required|boolean',
        'divisions' => 'nullable|json',
    ]);

    if (!empty($validated['divisions']) && is_array($validated['divisions'])) {
        $validated['divisions'] = json_encode($validated['divisions'], JSON_UNESCAPED_UNICODE);
    }

    $tournament = Tournament::findOrFail($id);
    $tournament->update(array_merge($validated, ['update_date' => now()]));

    return response()->json(['message' => '更新完了']);
    }

}