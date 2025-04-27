<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Game;
use Illuminate\Support\Facades\DB;

class GameController extends Controller
{
    // 試合一覧取得
    public function index()
{
    $games = DB::table('t_games')
        ->leftJoin('t_teams as team1', 't_games.team1_id', '=', 'team1.team_id')
        ->leftJoin('t_teams as team2', 't_games.team2_id', '=', 'team2.team_id')
        ->leftJoin('t_tournaments', 't_games.tournament_id', '=', 't_tournaments.tournament_id') 
        ->leftJoin('t_venues', 't_games.venue_id', '=', 't_venues.venue_id')
        ->select(
            't_games.game_id',
            't_tournaments.name as tournament_name', // 大会名
            't_tournaments.categoly as tournament_category',    // カテゴリ名
            't_games.division_name',                  // ディビジョン名
            't_games.round_label',                    // 回戦名（正式に使う方）
            't_games.game_date',                      // 開催日時（正式に使う方）
            't_venues.venue_name as venue_name',      // 会場
            'team1.team_name as team_name_a',          // チームA
            'team2.team_name as team_name_b',          // チームB
            't_games.approval_flg',                    // 承認フラグ
            // スコア（チームA 前半・後半）
            't_games.team1_score1st_point',
            't_games.team1_score2nd_point',

            // スコア（チームB 前半・後半）
            't_games.team2_score1st_point',
            't_games.team2_score2nd_point'
        )
        ->distinct()
        ->orderBy('t_games.game_date', 'asc')
        ->get();

    return response()->json($games);
}

    // 試合登録
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tournament_id' => 'required|exists:t_tournaments,tournament_id',
            'division_name' => 'nullable|string',
            'match_round' => 'nullable|string',
            'match_datetime' => 'required|date',
            'venue_id' => 'nullable|integer',
            'team1_id' => 'required|integer',
            'team1_alias' => 'nullable|string',
            'team2_id' => 'required|integer',
            'team2_alias' => 'nullable|string',
            'referee_id' => 'nullable|integer',
            'staff_id' => 'nullable|integer',
            'doctor_id' => 'nullable|integer',
            'team1_score1st_point' => 'nullable|integer',
            'team1_score2nd_point' => 'nullable|integer',
            'team2_score1st_point' => 'nullable|integer',
            'team2_score2nd_point' => 'nullable|integer',
            'approval_flg' => 'nullable|integer',
        ]);

        $game = new Game();
        $game->tournament_id = $validated['tournament_id'];
        $game->venue_id = $validated['venue_id'];
        $game->team1_id = $validated['team1_id'];
        $game->team2_id = $validated['team2_id'];
        $game->division_name = $validated['division_name'];
        $game->division_order = 0; // 今は仮0
        $game->round_label = $validated['match_round'];
        $game->game_date = $validated['match_datetime'];
        $game->team1_score1st_point = $validated['team1_score1st_point'] ?? 0;
        $game->team1_score2nd_point = $validated['team1_score2nd_point'] ?? 0;
        $game->team2_score1st_point = $validated['team2_score1st_point'] ?? 0;
        $game->team2_score2nd_point = $validated['team2_score2nd_point'] ?? 0;
        $game->approval_flg = $validated['approval_flg'] ?? 0;

        $game->save();

        return response()->json(['message' => '試合情報を登録しました', 'game' => $game], 201);
    }

    // 試合詳細取得
    public function show($id)
    {
        return Game::findOrFail($id);
    }

    // 試合更新
    public function update(Request $request, $id)
    {
        $game = Game::findOrFail($id);

        $validated = $request->validate([
            'division_name' => 'sometimes|string',
            'match_round' => 'nullable|string',
            'match_datetime' => 'sometimes|date',
            'venue_id' => 'nullable|integer',
            'team1_id' => 'sometimes|integer',
            'team1_alias' => 'nullable|string',
            'team2_id' => 'sometimes|integer',
            'team2_alias' => 'nullable|string',
            'referee_id' => 'nullable|integer',
            'staff_id' => 'nullable|integer',
            'doctor_id' => 'nullable|integer',
        ]);

        $game->update($validated);

        return response()->json(['message' => '試合情報を更新しました', 'game' => $game]);
    }

    // 試合削除
    public function destroy($id)
    {
        $game = Game::findOrFail($id);
        $game->delete();

        return response()->json(['message' => '試合情報を削除しました']);
    }
}
