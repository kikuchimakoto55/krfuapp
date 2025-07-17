<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\Rankup;
use Carbon\Carbon;

class RankupImportController extends Controller
{
    public function import(Request $request)
    {
        Log::info(' 年度更新CSVアップロード受信');

        // バリデーション
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:csv,txt',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'CSVファイルを選択してください'], 422);
        }

        $file = $request->file('file');
        $path = $file->getRealPath();

        // BOM除去＋読み込み
        $content = file_get_contents($path);
        $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);
        $rows = array_map('str_getcsv', explode("\n", $content));
        $header = array_map('trim', array_shift($rows));

        // 必須カラム確認
        $requiredHeaders = ['Text 99', 'Text 120', 'Select 531', 'Select 258', 'Select 757', 'Select 567'];
        foreach ($requiredHeaders as $column) {
            if (!in_array($column, $header)) {
                return response()->json(['message' => "CSVにカラム「{$column}」が存在しません。"], 400);
            }
        }

        // カラム位置特定
        $idxKanaS    = array_search('Text 99', $header);
        $idxKanaM    = array_search('Text 120', $header);
        $idxSex      = array_search('Select 531', $header);
        $idxBirthY   = array_search('Select 258', $header);
        $idxBirthM   = array_search('Select 757', $header);
        $idxBirthD   = array_search('Select 567', $header);

        $successCount = 0;
        $skipCount = 0;

        foreach ($rows as $row) {
            if (count($row) < 6) continue; // 空行対策

            $kanaS = trim($row[$idxKanaS] ?? '');
            $kanaM = trim($row[$idxKanaM] ?? '');
            $sex   = trim($row[$idxSex] ?? '');
            $y     = trim($row[$idxBirthY] ?? '');
            $m     = trim($row[$idxBirthM] ?? '');
            $d     = trim($row[$idxBirthD] ?? '');

            if (!$kanaS || !$kanaM || !$sex || !$y || !$m || !$d) {
                $skipCount++;
                continue;
            }

            try {
                Rankup::create([
                    'username_kana_s' => $kanaS,
                    'username_kana_m' => $kanaM,
                    'sex'             => $sex,
                    'birthday1'       => $y,
                    'birthday2'       => $m,
                    'birthday3'       => $d,
                    'rankup_flg'      => 0,
                ]);
                $successCount++;
            } catch (\Exception $e) {
                Log::error(' インポート失敗: ' . $e->getMessage());
                $skipCount++;
            }
        }

        return response()->json([
            'message' => 'CSVインポート完了',
            'imported' => $successCount,
            'skipped' => $skipCount,
        ]);
    }
}