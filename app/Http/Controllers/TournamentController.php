<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tournament;
use App\Models\Game;
use App\Models\Score;
use Illuminate\Support\Facades\DB;
use App\Models\TournamentResult;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class TournamentController extends Controller
{
    public function store(Request $request)
    {
        try {
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
        } catch (ValidationException $e) {
            Log::error('バリデーション失敗', $e->errors());
            return response()->json(['errors' => $e->errors()], 422);
        }

        if (empty($validated['divisions'])) {
            $validated['divisions'] = null;
        }

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

    public function index()
    {
        $tournaments = Tournament::where('del_flg', 0)
            ->orderBy('event_period_start', 'desc')
            ->get();

        return response()->json($tournaments);
    }

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

        if ($validated['divisionflg'] == 0) {
            $validated['divisions'] = json_encode([]);
        } else {
            if (is_array($validated['divisions'])) {
                $validated['divisions'] = json_encode($validated['divisions']);
            }

            $existingResults = TournamentResult::where('tournament_id', $id)
                ->where('del_flg', 0)
                ->get();

            if ($existingResults->isNotEmpty()) {
                $newDivisions = json_decode($validated['divisions'], true) ?? [];
                $existingMaxOrder = $existingResults->max('division_order');

                if (count($newDivisions) > $existingMaxOrder) {
                    return response()->json([
                        'message' => '大会結果が登録済みのため、ディビジョンの追加はできません。'
                    ], 422);
                }
            }
        }

        $tournament->update(array_merge($validated, ['update_date' => now()]));

        return response()->json(['message' => '更新完了']);
    }

    public function list()
    {
        $tournaments = DB::table('t_tournaments')
            ->select('tournament_id', 'name', 'year', 'categoly')
            ->where('del_flg', 0)
            ->orderBy('year', 'desc')
            ->orderBy('name')
            ->get();

        return response()->json($tournaments);
    }

    public function checkDivisionFlg($id)
    {
        $tournament = DB::table('t_tournaments')
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
        $divisions = DB::table('t_games')
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

    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            $tournament = Tournament::findOrFail($id);
            $tournament->update(['del_flg' => 1]);

            $games = Game::where('tournament_id', $id)->get();

            foreach ($games as $game) {
                $updated = Score::where('game_id', $game->game_id)->update(['del_flg' => 1]);
                Log::info("スコア del_flg 更新件数：{$updated}（game_id = {$game->game_id}）");
                $game->update(['del_flg' => 1]);
            }
        });

        return response()->json(['message' => '大会および関連データを論理削除しました']);
    }
}
