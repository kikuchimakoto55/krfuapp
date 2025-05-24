<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Game;
use Illuminate\Support\Facades\DB;
use App\Models\Score;

class GameController extends Controller
{
    // 試合一覧取得
    public function index()
{
    $games = DB::table('t_games')
        ->where('t_games.del_flg', 0) // ✅ 論理削除除外
        ->leftJoin('t_teams as team1', 't_games.team1_id', '=', 'team1.team_id')
        ->leftJoin('t_teams as team2', 't_games.team2_id', '=', 'team2.team_id')
        ->leftJoin('t_tournaments', 't_games.tournament_id', '=', 't_tournaments.tournament_id') 
        ->leftJoin('t_venues', 't_games.venue_id', '=', 't_venues.venue_id')
        ->leftJoin('t_scores', 't_games.game_id', '=', 't_scores.game_id')
        ->select(
            't_games.game_id',
            't_games.division_order',
            't_tournaments.name as tournament_name',
            't_tournaments.categoly as tournament_category',
            't_games.division_name',
            't_games.round_label',
            't_games.game_date',
            't_venues.venue_name as venue_name',
            'team1.team_name as team_name_a',
            'team2.team_name as team_name_b',
            't_games.approval_flg',
            't_scores.op1fh_score as team1_score1st_point',
            't_scores.op1hh_score as team1_score2nd_point',
            't_scores.op2fh_score as team2_score1st_point',
            't_scores.op2hh_score as team2_score2nd_point'
        )
        ->distinct()
        ->orderBy('t_games.game_date', 'asc')
        ->get();

    return response()->json($games);
}

// 試合検索
public function search(Request $request)
{
    $query = DB::table('t_games')
        ->where('t_games.del_flg', 0) //  論理削除除外
        ->join('t_tournaments', 't_games.tournament_id', '=', 't_tournaments.tournament_id')
        ->leftJoin('t_teams as team1', 't_games.team1_id', '=', 'team1.team_id')
        ->leftJoin('t_teams as team2', 't_games.team2_id', '=', 'team2.team_id')
        ->leftJoin('t_venues', 't_games.venue_id', '=', 't_venues.venue_id')
        ->leftJoin('t_scores', 't_games.game_id', '=', 't_scores.game_id')
        ->select(
            't_games.game_id',
            't_games.division_order',
            't_tournaments.name as tournament_name',
            't_tournaments.categoly as tournament_category',
            't_games.division_name',
            't_games.round_label',
            't_games.game_date',
            't_venues.venue_name as venue_name',
            'team1.team_name as team_name_a',
            'team2.team_name as team_name_b',
            't_games.approval_flg',
            't_scores.op1fh_score as team1_score1st_point',
            't_scores.op1hh_score as team1_score2nd_point',
            't_scores.op2fh_score as team2_score1st_point',
            't_scores.op2hh_score as team2_score2nd_point'
        );


    // 条件追加
    if ($request->filled('categoly')) {
        $query->where('t_tournaments.categoly', $request->input('categoly'));
    }
    if ($request->filled('year')) {
        $query->where('t_tournaments.year', $request->input('year'));
    }
    if ($request->filled('tournament_id')) {
        $query->where('t_games.tournament_id', $request->input('tournament_id'));
    }
    if ($request->filled('division_name')) {
        $query->where('t_games.division_name', 'like', '%' . $request->input('division_name') . '%');
    }
    if ($request->filled('match_datetime')) {
        $query->whereDate('t_games.game_date', $request->input('match_datetime'));
    }
    if ($request->filled('team_name')) {
        $query->where(function ($q) use ($request) {
            $q->where('team1.team_name', 'like', '%' . $request->team_name . '%')
              ->orWhere('team2.team_name', 'like', '%' . $request->team_name . '%');
        });
    }

    
    $games = $query->paginate(20);
    return response()->json($games);
    }

    // 試合登録
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tournament_id' => 'required|exists:t_tournaments,tournament_id',
            'division_name' => 'nullable|string',
            'division_order' => 'nullable|integer',
            'match_round' => 'nullable|string',
            'match_datetime' => 'sometimes|date',
            'venue_id' => 'nullable|integer',
            'team1_id' => 'sometimes|integer',
            'team2_id' => 'sometimes|integer',
            'referee' => 'nullable|string',
            'manager' => 'nullable|string',
            'doctor' => 'nullable|string',
            'score' => 'nullable|array',
            
            // score 内の各フィールドを明示的に
            'score.op1fh_score' => 'nullable|integer',
            'score.op1hh_score' => 'nullable|integer',
            'score.op2fh_score' => 'nullable|integer',
            'score.op2hh_score' => 'nullable|integer',
            'score.op1fh_t' => 'nullable|integer',
            'score.op1fh_g' => 'nullable|integer',
            'score.op1fh_pg' => 'nullable|integer',
            'score.op1fh_dg' => 'nullable|integer',
            'score.op1fh_pkscore' => 'nullable|integer',
            'score.op1fh_fkscore' => 'nullable|integer',
            'score.op1hh_t' => 'nullable|integer',
            'score.op1hh_g' => 'nullable|integer',
            'score.op1hh_pg' => 'nullable|integer',
            'score.op1hh_dg' => 'nullable|integer',
            'score.op1hh_pkscore' => 'nullable|integer',
            'score.op1hh_fkscore' => 'nullable|integer',
            'score.op2fh_t' => 'nullable|integer',
            'score.op2fh_g' => 'nullable|integer',
            'score.op2fh_pg' => 'nullable|integer',
            'score.op2fh_dg' => 'nullable|integer',
            'score.op2fh_pkscore' => 'nullable|integer',
            'score.op2fh_fkscore' => 'nullable|integer',
            'score.op2hh_t' => 'nullable|integer',
            'score.op2hh_g' => 'nullable|integer',
            'score.op2hh_pg' => 'nullable|integer',
            'score.op2hh_dg' => 'nullable|integer',
            'score.op2hh_pkscore' => 'nullable|integer',
            'score.op2hh_fkscore' => 'nullable|integer',
            'score.score_book' => 'nullable|string',
            'score.gamereport' => 'nullable|string',
            'score.publishing' => 'nullable|integer',
        ]);

        $game = new Game();
        $game->tournament_id = $validated['tournament_id'];
        $game->venue_id = $validated['venue_id'];
        $game->team1_id = $validated['team1_id'];
        $game->team2_id = $validated['team2_id'];
        $game->division_name = $validated['division_name'];
        $game->division_order = $validated['division_order'] ?? null; // ✅ ここだけにする
        $game->round_label = $validated['match_round'];
        $game->game_date = $validated['match_datetime'];
        $game->approval_flg = $validated['approval_flg'] ?? 0;
        $game->referee = $validated['referee'] ?? null;
        $game->manager = $validated['manager'] ?? null;
        $game->doctor = $validated['doctor'] ?? null;
        $game->del_flg = 0;

        $game->save();

        return response()->json(['message' => '試合情報を登録しました', 'game' => $game], 201);
    }

    // 試合詳細取得
    public function show($id)
{
    $game = Game::with(['team1', 'team2', 'venue', 'score'])
        ->where('game_id', $id)
        ->where('del_flg', 0)
        ->firstOrFail();

    if ($game->score) {
        $game->score->score_book = $game->score->score_book ?? '';
    }
    \Log::debug('division_order = ' . $game->division_order);

    return response()->json([
        'game_id' => $game->game_id,
        'tournament_id' => $game->tournament_id,
        'division_order' => $game->division_order, // ← ★これを明示的に
        'division_name' => $game->division_name,
        'round_label' => $game->round_label,
        'game_date' => $game->game_date,
        'venue_id' => $game->venue_id,
        'team1_id' => $game->team1_id,
        'team2_id' => $game->team2_id,
        'referee' => $game->referee,
        'manager' => $game->manager,
        'doctor' => $game->doctor,
        'score' => $game->score,
        'team1' => $game->team1,
        'team2' => $game->team2,
        'venue' => $game->venue,
    ]);
    
}

    // 試合更新
    public function update(Request $request, $id)
    {
        \Log::debug('GameController@update called', [
            'id' => $id,
            'input' => $request->all(),
        ]);
        $game = Game::findOrFail($id);

        $validated = $request->validate([
            'division_name' => 'nullable|string',
            'match_round' => 'nullable|string',
            'match_datetime' => 'sometimes|date',
            'venue_id' => 'nullable|integer',
            'team1_id' => 'sometimes|integer',
            'team2_id' => 'sometimes|integer',
            'referee' => 'nullable|string',
            'manager' => 'nullable|string',
            'doctor' => 'nullable|string',
            'score' => 'nullable|array',
            'game_report' => 'nullable|string',
            'publishing' => 'nullable|integer',
            
            // score 内の各フィールドを明示的に
            'score.op1fh_score' => 'nullable|integer',
            'score.op1hh_score' => 'nullable|integer',
            'score.op2fh_score' => 'nullable|integer',
            'score.op2hh_score' => 'nullable|integer',
            'score.op1fh_t' => 'nullable|integer',
            'score.op1fh_g' => 'nullable|integer',
            'score.op1fh_pg' => 'nullable|integer',
            'score.op1fh_dg' => 'nullable|integer',
            'score.op1fh_pkscore' => 'nullable|integer',
            'score.op1fh_fkscore' => 'nullable|integer',
            'score.op1hh_t' => 'nullable|integer',
            'score.op1hh_g' => 'nullable|integer',
            'score.op1hh_pg' => 'nullable|integer',
            'score.op1hh_dg' => 'nullable|integer',
            'score.op1hh_pkscore' => 'nullable|integer',
            'score.op1hh_fkscore' => 'nullable|integer',
            'score.op2fh_t' => 'nullable|integer',
            'score.op2fh_g' => 'nullable|integer',
            'score.op2fh_pg' => 'nullable|integer',
            'score.op2fh_dg' => 'nullable|integer',
            'score.op2fh_pkscore' => 'nullable|integer',
            'score.op2fh_fkscore' => 'nullable|integer',
            'score.op2hh_t' => 'nullable|integer',
            'score.op2hh_g' => 'nullable|integer',
            'score.op2hh_pg' => 'nullable|integer',
            'score.op2hh_dg' => 'nullable|integer',
            'score.op2hh_pkscore' => 'nullable|integer',
            'score.op2hh_fkscore' => 'nullable|integer',
            'score.score_book' => 'nullable|string',
            'score.gamereport' => 'nullable|string',
            'score.publishing' => 'nullable|integer',
        ]);

        // 基本情報（readonlyの項目は除く）を更新

        if (array_key_exists('venue_id', $validated)) $game->venue_id = $validated['venue_id'];
        if (array_key_exists('team1_id', $validated)) $game->team1_id = $validated['team1_id'];
        if (array_key_exists('team2_id', $validated)) $game->team2_id = $validated['team2_id'];
        if (array_key_exists('match_round', $validated)) $game->round_label = $validated['match_round'];
        if (array_key_exists('match_datetime', $validated)) $game->game_date = $validated['match_datetime'];
        if (array_key_exists('referee', $validated)) $game->referee = $validated['referee'];
        if (array_key_exists('manager', $validated)) $game->manager = $validated['manager'];
        if (array_key_exists('doctor', $validated)) $game->doctor = $validated['doctor'];
        $game->save();

        // スコア・反則更新
if (isset($validated['score'])) {
    $scoreData = collect($validated['score'])->only([
        // チーム1前半・後半
        'op1fh_t', 'op1fh_g', 'op1fh_pg', 'op1fh_dg',
        'op1fh_score', 'op1fh_pkscore', 'op1fh_fkscore',
        'op1hh_t', 'op1hh_g', 'op1hh_pg', 'op1hh_dg',
        'op1hh_score', 'op1hh_pkscore', 'op1hh_fkscore',

        // チーム2前半・後半
        'op2fh_t', 'op2fh_g', 'op2fh_pg', 'op2fh_dg',
        'op2fh_score', 'op2fh_pkscore', 'op2fh_fkscore',
        'op2hh_t', 'op2hh_g', 'op2hh_pg', 'op2hh_dg',
        'op2hh_score', 'op2hh_pkscore', 'op2hh_fkscore',

        // その他（オプション）
        'score_book', 'gamereport', 'publishing'
        
    ])->toArray();
    $scoreData['gamereport'] = $validated['game_report'] ?? null;
    $scoreData['publishing'] = $validated['publishing'] ?? 1;

    // ファイルがある場合はパスを追加
    if ($request->hasFile('scorebook')) {
    $paths = [];

    foreach ($request->file('scorebook') as $file) {
        // game_id ごとのサブディレクトリに保存
        $paths[] = $file->store("scorebooks/{$game->game_id}", 'public');
    }

    // カンマ区切りで保存（DBのscore_bookカラム）
    $scoreData['score_book'] = implode(',', $paths);
    }

    // 保存
    Score::updateOrCreate(
        ['game_id' => $game->game_id],
        $scoreData
    );


}


        return response()->json(['message' => '試合情報を更新しました', 'game' => $game]);
    }

    // 試合削除
    public function destroy($id)
    {
    $game = Game::findOrFail($id);
    $game->update(['del_flg' => 1]);

    Score::where('game_id', $game->game_id)->update(['del_flg' => 1]);

    return response()->json(['message' => '試合情報を論理削除しました']);
    }

}
