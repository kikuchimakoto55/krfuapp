<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TournamentResult;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\UpdateTournamentResultRequest;


class TournamentResultController extends Controller
{
    /**
     * 共通保存処理（新規登録・更新共通）
     */
    private function saveTournamentResults(array $results, int $tournamentId): void
    {
        foreach ($results as $data) {
            $documentPath = null;

            if (!empty($data['document']) && $data['document'] instanceof \Illuminate\Http\UploadedFile) {
                $timestampDir = now()->format('Ymd_His');
                $uniqueName = Str::uuid() . '_' . $data['document']->getClientOriginalName();

                $documentPath = $data['document']->storeAs(
                    "tournament_results/{$timestampDir}",
                    $uniqueName,
                    'public'
                );
            }

            TournamentResult::create([
                'tournament_id'   => $tournamentId,
                'division_order'  => $data['division_order'],
                'division_name'   => $data['division_name'],
                'rank_order'      => $data['rank_order'],
                'rank_label'      => $data['rank_label'],
                'team_id'         => $data['team_id'] ?? null,
                'report'          => $data['report'] ?? null,
                'document_path'   => $documentPath,
                'del_flg'         => 0,
            ]);
        }
    }

    /**
     * 新規登録
     */
    public function store(Request $request)
    {
        $request->validate([
            'tournament_id' => 'required|integer|exists:t_tournaments,tournament_id',
            'results' => 'required|array',
        ]);

        DB::beginTransaction();

        try {
            $flatResults = [];

            foreach ($request->results as $divisionOrder => $divisionResults) {
                foreach ($divisionResults as $index => $data) {
                    validator($data, [
                        'division_order' => 'required|integer',
                        'division_name' => 'required|string|max:100',
                        'rank_order' => 'required|integer',
                        'rank_label' => 'required|string|max:50',
                        'team_id' => 'nullable|integer|exists:t_teams,id',
                        'report' => 'nullable|string',
                        'document' => 'nullable|file|max:10240',
                    ])->validate();

                    $fileKey = "results.$divisionOrder.$index.document";
                    if ($request->hasFile($fileKey)) {
                        $data['document'] = $request->file($fileKey);
                    }

                    $flatResults[] = $data;
                }
            }

            $this->saveTournamentResults($flatResults, $request->tournament_id);

            DB::commit();

            return response()->json([
                'message' => '登録完了',
                'tournament_id' => $request->tournament_id,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => '登録に失敗しました',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * 更新（論理削除 → 再登録）
     */
    public function update(Request $request, $tournament_id)
{
    Log::debug('📥 TournamentResult update request received.');

    // ✅ FormData形式（results[n][field]）で送信されたネスト構造をそのまま取得
    $results = $request->input('results');
    Log::debug('📦 input results:', $results);

    // ✅ ファイル（document）がある場合はマージする
    foreach ($results as $index => &$result) {
        if ($request->hasFile("results.$index.document")) {
            $result['document'] = $request->file("results.$index.document");
        }
    }
    unset($result);

    Log::debug('✅ ファイル統合後 results:', $results);

    if (empty($results)) {
        Log::error('📛 results フィールドが存在しないため終了');
        return response()->json(['error' => 'results not found'], 422);
    }

    // ✅ バリデーション定義
    $validator = Validator::make([
        'tournament_id' => $tournament_id,
        'results' => $results
    ], [
        'tournament_id' => 'required|integer|exists:t_tournaments,tournament_id',
        'results' => 'required|array',
        'results.*.division_order' => 'required|integer',
        'results.*.division_name' => 'required|string|max:100',
        'results.*.rank_order' => 'required|integer',
        'results.*.rank_label' => 'required|string|max:50',
        'results.*.team_id' => 'nullable|integer|exists:t_teams,id',
        'results.*.report' => 'nullable|string',
        'results.*.document' => 'nullable|file|max:10240',
    ]);

    if ($validator->fails()) {
        Log::error('❌ バリデーションエラー', [
            'errors' => $validator->errors()->toArray(),
            'validated_input' => $results,
        ]);
        return response()->json([
            'message' => 'バリデーションに失敗しました',
            'errors' => $validator->errors(),
        ], 422);
    }

    // 🔁 既存データを del_flg = 1 に更新
    TournamentResult::where('tournament_id', $tournament_id)->update(['del_flg' => 1]);

    // ✅ データ再登録
    foreach ($results as $item) {
        $path = null;
        if (!empty($item['document'])) {
            $path = $item['document']->store('tournament_documents', 'public');
        }

        TournamentResult::create([
            'tournament_id' => $tournament_id,
            'division_order' => $item['division_order'],
            'division_name' => $item['division_name'],
            'rank_order' => $item['rank_order'],
            'rank_label' => $item['rank_label'],
            'team_id' => $item['team_id'] ?? null,
            'report' => $item['report'] ?? null,
            'document_path' => $path,
            'del_flg' => 0,
        ]);
    }

    return response()->json(['message' => '大会結果を更新しました']);
}

    /**
     * 結果詳細取得（チーム名付き）
     */
    public function show($id)
    {
        $results = TournamentResult::where('tournament_id', $id)
            ->where('del_flg', 0)
            ->with('team')
            ->orderBy('division_order')
            ->orderBy('rank_order')
            ->get()
            ->map(function ($result) {
                return [
                    'division_order' => $result->division_order,
                    'division_name'  => $result->division_name,
                    'rank_order'     => $result->rank_order,
                    'rank_label'     => $result->rank_label,
                    'team_id'        => $result->team_id,
                    'team_name'      => optional($result->team)->team_name,
                    'report'         => $result->report,
                    'document_path'  => $result->document_path,
                ];
            });

        return response()->json($results);
    }

    /**
     * クエリ取得（tournament_id指定）
     */
    public function index(Request $request)
    {
        if ($request->has('tournament_id')) {
            $results = TournamentResult::where('tournament_id', $request->tournament_id)
                ->where('del_flg', 0)
                ->get();

            return response()->json($results);
        }

        return response()->json([], 400);
    }
}