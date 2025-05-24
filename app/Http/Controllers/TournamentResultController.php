<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TournamentResult;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class TournamentResultController extends Controller
{
    public function store(Request $request)
{
    $request->validate([
        'tournament_id' => 'required|integer|exists:t_tournaments,tournament_id',
        'results' => 'required|array',
        'results.*.*.division_order' => 'required|integer',
        'results.*.*.division_name' => 'required|string|max:100',
        'results.*.*.rank_order' => 'required|integer',
        'results.*.*.rank_label' => 'required|string|max:50',
        'results.*.*.team_id' => 'required|integer|exists:t_teams,id',
        'results.*.*.report' => 'nullable|string',
        'results.*.*.document' => 'nullable|file|max:10240', // 10MB
    ]);

    DB::beginTransaction();

    try {
        foreach ($request->results as $divisionOrder => $divisionResults) {
            foreach ($divisionResults as $index => $data) {
                $filePath = null;
                $fileKey = "results.$divisionOrder.$index.document";

                if ($request->hasFile($fileKey)) {
                        $originalName = $request->file($fileKey)->getClientOriginalName();
                        $timestampDir = now()->format('Ymd_His');
                        $filePath = $request->file($fileKey)->storeAs("tournament_results/{$timestampDir}", $originalName, 'public');
                    }

                TournamentResult::create([
                    'tournament_id'   => $request->tournament_id,
                    'division_order'  => $data['division_order'],
                    'division_name'   => $data['division_name'],
                    'rank_order'      => $data['rank_order'],
                    'rank_label'      => $data['rank_label'],
                    'team_id'         => $data['team_id'],
                    'report'          => $data['report'] ?? null,
                    'document_path'   => $filePath,
                    'del_flg'         => 0,
                    
                ]);
            }
        }

        DB::commit();
        return response()->json(['message' => '登録完了'], 201);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['error' => '登録に失敗しました', 'details' => $e->getMessage()], 500);
    }
}


    public function showByTournament($tournamentId)
{
    $results = TournamentResult::where('tournament_id', $tournamentId)
        ->with('team') //  チーム情報も含める
        ->orderBy('division_order')
        ->orderBy('rank_order')
        ->get()
        ->map(function ($result) {
            return [
                'division_order' => $result->division_order,
                'division_name' => $result->division_name,
                'rank_order' => $result->rank_order,
                'rank_label' => $result->rank_label,
                'team_name' => optional($result->team)->team_name, //  ここで追加
                'report' => $result->report,
                'document_path' => $result->document_path,
            ];
        });

    return response()->json($results);
}

public function show($tournamentId)
{
    $results = TournamentResult::where('tournament_id', $tournamentId)
        ->with('team')
        ->orderBy('division_order')
        ->orderBy('rank_order')
        ->get()
        ->map(function ($result) {
            return [
                'division_order' => $result->division_order,
                'division_name' => $result->division_name,
                'rank_order' => $result->rank_order,
                'rank_label' => $result->rank_label,
                'team_name' => optional($result->team)->team_name,
                'report' => $result->report,
                'document_path' => $result->document_path,
            ];
        });

    return response()->json($results);
}


public function updateByTournament(Request $request, $tournamentId)
{
    $request->validate([
        'results' => 'required|array',
        'results.*.*.division_order' => 'required|integer',
        'results.*.*.division_name' => 'required|string|max:100',
        'results.*.*.rank_order' => 'required|integer',
        'results.*.*.rank_label' => 'required|string|max:50',
        'results.*.*.team_id' => 'required|integer|exists:t_teams,id',
        'results.*.*.report' => 'nullable|string',
        'results.*.*.document' => 'nullable|file|max:10240', // 10MB
    ]);

    DB::beginTransaction();

    try {
        // 既存の結果を削除（論理削除）
        TournamentResult::where('tournament_id', $tournamentId)->update(['del_flg' => 1]);

        foreach ($request->results as $divisionOrder => $divisionResults) {
            foreach ($divisionResults as $index => $data) {
                $filePath = null;
                $fileKey = "results.$divisionOrder.$index.document";

                if ($request->hasFile($fileKey)) {
                    $originalName = $request->file($fileKey)->getClientOriginalName();
                    $timestampDir = now()->format('Ymd_His');
                    $filePath = $request->file($fileKey)->storeAs("tournament_results/{$timestampDir}", $originalName, 'public');
                }

                TournamentResult::create([
                    'tournament_id'   => $tournamentId,
                    'division_order'  => $data['division_order'],
                    'division_name'   => $data['division_name'],
                    'rank_order'      => $data['rank_order'],
                    'rank_label'      => $data['rank_label'],
                    'team_id'         => $data['team_id'],
                    'report'          => $data['report'] ?? null,
                    'document_path'   => $filePath,
                    'del_flg'         => 0,
                ]);
            }
        }

        DB::commit();
        return response()->json(['message' => '大会結果を更新しました']);

    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'error' => '更新に失敗しました',
            'details' => $e->getMessage()
        ], 500);
    }
}

}
