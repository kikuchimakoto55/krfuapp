<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Team;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TeamsImportController extends Controller
{
    // プレビュー処理（ファイル読み込み）
    public function preview(Request $request)
    {
    $request->validate([
        'file' => 'required|file|mimes:csv,txt'
    ]);

    $file = $request->file('file');
    $handle = fopen($file->getRealPath(), 'r');
    $header = fgetcsv($handle);

    $preview = [];
    $errors = [];

    $rowNum = 1; // 1行目はヘッダーなのでスキップ

    while (($row = fgetcsv($handle)) !== false) {
        $rowNum++;
        $record = array_combine($header, $row);

        if (!$record) continue;

        // 重複チェック（year + team_id）
        $exists = Team::where('year', $record['year'])
                      ->where('team_id', $record['team_id'])
                      ->exists();

        if ($exists) {
            $record['__update__'] = true; // 追加フラグ
        } else {
            $record['__update__'] = false;
        }

        $preview[] = $record;
    }

    fclose($handle);

    return response()->json([
        'data' => $preview,
        'valid' => true,
        'errors' => [],
    ]);
    }


    // 登録処理（存在チェック → 更新 or 新規登録）
    public function import(Request $request)
{
    $rules = [
        'data' => 'required|array',
        'data.*.team_id' => 'required|integer',
        'data.*.year' => 'required|integer',
        'data.*.team_name' => 'required|string|max:255',
        'data.*.representative_name' => 'nullable|string|max:255',
        'data.*.representative_kana' => 'nullable|string|max:255',
        'data.*.representative_tel' => 'nullable|string|max:20',
        'data.*.representative_email' => 'nullable|email|max:255',
        'data.*.male_members' => 'nullable|integer|min:0',
        'data.*.female_members' => 'nullable|integer|min:0',
        'data.*.medical_supporter' => 'nullable|string|max:255',
        'data.*.jrfu_coach' => 'nullable|string|max:255',
        'data.*.safety_lecturer' => 'nullable|string|max:255',
        'data.*.category' => 'nullable|integer|min:1|max:9',
        'data.*.status' => 'nullable|integer|min:0|max:2',
        'data.*.annual_fee_flg' => 'nullable|boolean',
        'data.*.individual_entry_flg' => 'nullable|boolean',
        'data.*.team_entry_flg' => 'nullable|boolean',
    ];

    $attributes = [
        'data.*.team_id' => 'チームID',
        'data.*.year' => '年度',
        'data.*.team_name' => 'チーム名',
        'data.*.representative_name' => '代表者氏名',
        'data.*.representative_kana' => '代表者カナ',
        'data.*.representative_tel' => '代表者電話番号',
        'data.*.representative_email' => '代表者メール',
        'data.*.male_members' => '男子人数',
        'data.*.female_members' => '女子人数',
        'data.*.medical_supporter' => 'メディカルサポーター',
        'data.*.jrfu_coach' => 'JRFUコーチ',
        'data.*.safety_lecturer' => 'セーフティ講習会修了者',
        'data.*.category' => 'カテゴリ',
        'data.*.status' => 'ステータス',
        'data.*.annual_fee_flg' => '年会費フラグ',
        'data.*.individual_entry_flg' => '個人申込フラグ',
        'data.*.team_entry_flg' => 'チーム申込フラグ',
    ];

    // Validatorを手動で使って属性名を日本語化
    $validator = \Validator::make($request->all(), $rules, [], $attributes);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $count = 0;
    foreach ($request->input('data') as $row) {
        $team = Team::where('year', $row['year'])
                    ->where('team_id', $row['team_id'])
                    ->first();

        if ($team) {
            $team->fill($row);
            $team->save();
        } else {
            Team::create($row);
        }
        $count++;
    }

    return response()->json(['message' => "{$count}件の登録が完了しました"]);
}


    //ダウンロード
    public function export()
    {
    $filename = 'teams_export.csv';
    $teams = Team::all();

    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => "attachment; filename={$filename}",
    ];

    $callback = function () use ($teams) {
        $handle = fopen('php://output', 'w');

        // ヘッダー行
        fputcsv($handle, [
            'year',
            'team_id',
            'team_name',
            'representative_name',
            'representative_kana',
            'representative_tel',
            'representative_email',
            'male_members',
            'female_members',
            'medical_supporter',
            'jrfu_coach',
            'safety_lecturer',
            'category',
            'status',
            'annual_fee_flg',
            'individual_entry_flg',
            'team_entry_flg',
        ]);

        // データ行
        foreach ($teams as $team) {
        try {
            fputcsv($handle, [
                $team->year ?? '',
            $team->team_id ?? '',
            $team->team_name ?? '',
            $team->representative_name ?? '',
            $team->representative_kana ?? '',
            $team->representative_tel ?? '',
            $team->representative_email ?? '',
            $team->male_members ?? '',
            $team->female_members ?? '',
            $team->medical_supporter ?? '',
            $team->jrfu_coach ?? '',
            $team->safety_lecturer ?? '',
            $team->category ?? '',
            $team->status ?? '',
            $team->annual_fee_flg ?? '',
            $team->individual_entry_flg ?? '',
            $team->team_entry_flg ?? '',
            ]);
        } catch (\Throwable $e) {
            Log::error('エクスポートエラー', [
                'team_id' => $team->team_id ?? 'unknown',
                'message' => $e->getMessage()
            ]);
        }
    }
        fclose($handle);
    };

    return new StreamedResponse($callback, 200, $headers);
    }
}
