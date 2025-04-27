<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TournamentResult;
use Illuminate\Support\Facades\Storage;

class TournamentResultController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'tournament_id' => 'required|integer|exists:t_tournaments,tournament_id',
            'results' => 'required|array',
        ]);

        foreach ($request->results as $divisionOrder => $divisionResults) {
            foreach ($divisionResults as $index => $data) {
                $filePath = null;
                $fileKey = "results.$divisionOrder.$index.document";

                if ($request->hasFile($fileKey)) {
                    $filePath = $request->file($fileKey)->store('tournament_results', 'public');
                }

                // 既存レコード取得 or 新規作成
                $result = TournamentResult::firstOrNew([
                    'tournament_id' => $request->tournament_id,
                    'division_order' => $data['division_order'],
                    'rank_label' => $data['rank_label'],
                ]);

                // 値をセット
                $result->division_name = $data['division_name'];
                $result->team_id = $data['team_id'];
                $result->report = $data['report'];

                // ファイルがある場合のみ更新
                if ($filePath !== null) {
                    $result->document_path = $filePath;
                }

                $result->save();
            }
        }

        return response()->json(['message' => '登録完了'], 201);
    }

    public function showByTournament($tournamentId)
    {
    $results = TournamentResult::where('tournament_id', $tournamentId)
        ->with('team') // チーム名も出したければリレーションも
        ->orderBy('division_order')
        ->orderBy('rank_label')
        ->get();

    return response()->json($results);
    }
}
