<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tournament;
use App\Models\Game;
use App\Models\Score;
use Illuminate\Support\Facades\DB;

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

        //  divisions を JSON 文字列にする
            if (!empty($validated['divisions']) && is_array($validated['divisions'])) {
            $validated['divisions'] = json_encode($validated['divisions'], JSON_UNESCAPED_UNICODE);
        }else {
            $validated['divisions'] = null;
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
    $tournaments = Tournament::where('del_flg', 0)
        ->orderBy('event_period_start', 'desc')
        ->get();

    return response()->json($tournaments);
    }

    // tournaments編集処理
    public function show($id)
    {
    $tournament = Tournament::where('tournament_id', $id)
        ->where('del_flg', 0)
        ->firstOrFail();

    $tournament->divisions = $tournament->divisions ? json_decode($tournament->divisions, true) : [];
    return response()->json($tournament);
    }

    public function update(Request $request, $id)
    {
        $tournament = Tournament::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'categoly' => 'required|integer',
            'year' => 'required|digits:4',
            'event_period_start' => 'required|date',
            'event_period_end' => 'nullable|date',
            'publishing' => 'required|boolean',
            'divisionflg' => 'required|boolean',
            'divisions' => 'nullable|string',
        
    ]);
    //  divisionflg = 0 のとき、divisionsを強制的に空にする
    if ($validated['divisionflg'] == 0) {
        //  一度も設定されたことがない新規大会：NULLのまま
        //  今回、設定解除した場合："[]" に上書き
    $validated['divisions'] = json_encode([]);
    } else {
    // divisions が配列なら json_encode、文字列ならそのまま
    if (is_array($validated['divisions'])) {
        $validated['divisions'] = json_encode($validated['divisions']);
    }
    }
    
    $tournament->update(array_merge($validated, ['update_date' => now()]));

    return response()->json(['message' => '更新完了']);
    }

    public function list()
    {
    $tournaments = \DB::table('t_tournaments')
        ->select('tournament_id', 'name', 'year', 'categoly')
        ->where('del_flg', 0) // 削除フラグが立ってない大会だけ
        ->orderBy('year', 'desc')
        ->orderBy('name')
        ->get();

    return response()->json($tournaments);
    }

    // 指定IDの divisionflg だけ返すAPI（軽量）
    public function checkDivisionFlg($id)
    {
        $tournament = \DB::table('t_tournaments')
            ->select('divisionflg')
            ->where('tournament_id', $id)
            ->where('del_flg', 0)
            ->first();

        if (!$tournament) {
            return response()->json(['message' => 'Not found'], 404);
        }

        return response()->json($tournament);

    }
    public function divisions($id)
    {
        $divisions = \DB::table('t_games')
            ->select('division_order', 'division_name')
            ->where('tournament_id', $id)
            ->where('del_flg', 0)
            ->whereNotNull('division_name')
            ->groupBy('division_order', 'division_name')
            ->orderBy('division_name')
            ->get();

    return response()->json($divisions);
    }


    public function search(Request $request)
{
    $query = Tournament::query();

    // ✅ 追加：削除されていないデータに限定
    $query->where('del_flg', 0);

    if ($request->filled('categoly')) {
        $query->where('categoly', $request->categoly);
    }

    if ($request->filled('year')) {
        $query->where('year', $request->year);
    }

    if ($request->filled('name')) {
        $query->where('name', 'like', '%' . $request->name . '%');
    }

    if ($request->filled('event_period_start')) {
        $query->whereDate('event_period_start', '>=', $request->event_period_start);
    }

    if ($request->filled('publishing')) {
        $query->where('publishing', $request->publishing);
    }

    if ($request->filled('divisionflg')) {
        $query->where('divisionflg', $request->divisionflg);
    }

    return response()->json([
        'data' => $query->orderBy('year', 'desc')->orderBy('event_period_start', 'desc')->get()
    ]);
    }

    //大会削除
    public function destroy($id)
    {
    DB::transaction(function () use ($id) {
        // トーナメント
        $tournament = Tournament::findOrFail($id);
        $tournament->update(['del_flg' => 1]);

        // 該当大会のゲーム
        $games = Game::where('tournament_id', $id)->get();

        foreach ($games as $game) {
            // スコアの論理削除
            $updated = Score::where('game_id', $game->game_id)->update(['del_flg' => 1]);
            \Log::info("スコア del_flg 更新件数：{$updated}（game_id = {$game->game_id}）");

            // ゲームの論理削除
            $game->update(['del_flg' => 1]);
        }
    });

    return response()->json(['message' => '大会および関連データを論理削除しました']);
    }


}