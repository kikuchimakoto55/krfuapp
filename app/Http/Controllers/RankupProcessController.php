<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Rankup;
use App\Models\Member;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Str;
use Carbon\Carbon;


class RankupProcessController extends Controller
{
    public function process()
    {
        Log::info(' 年度更新処理 開始');

        // Step 1: 重複除去
        $all = Rankup::where('rankup_flg', 0)->get();
        $uniqueKeys = [];

        foreach ($all as $record) {
            $key = implode('_', [
                $record->username_kana_s,
                $record->username_kana_m,
                $record->sex,
                $record->birthday1,
                $record->birthday2,
                $record->birthday3,
            ]);
            if (in_array($key, $uniqueKeys)) {
                $record->delete();
            } else {
                $uniqueKeys[] = $key;
            }
        }

        // Step 2: 照合と更新
        $records = Rankup::where('rankup_flg', 0)->get();
        $updatedCount = 0;
        $unmatchedRecords = [];

        foreach ($records as $r) {
            $fullBirthday = Carbon::createFromDate($r->birthday1, $r->birthday2, $r->birthday3)->format('Y-m-d');

            $kana_s = mb_convert_kana(trim($r->username_kana_s), 'KV');
            $kana_m = mb_convert_kana(trim($r->username_kana_m), 'KV');
            $csvSex = trim($r->sex ?? '');
            $sex = match($csvSex) {
                '男' => 1,
                '女' => 2,
                default => null,
            };

            // ログ出力（デバッグ目的）
            Log::info("CSV性別原文: {$r->sex} / トリム後: {$csvSex} / 数値化: {$sex}");
            

            $member = Member::where('username_kana_s', $kana_s)
                ->where('username_kana_m', $kana_m)
                ->where('sex', $sex)
                ->where('birthday', $fullBirthday)
                ->first();

            if ($member) {
                DB::transaction(function () use ($member, $r) {
                    if ((string)$member->grade_category === '13') {
                        $member->grade_category = '22';
                        $member->status = 5;
                    } else {
                        $member->grade_category = (int)$member->grade_category + 1;
                    }
                    $member->save();
                    $r->rankup_flg = 1;
                    $r->save();
                });
                $updatedCount++;
            } else {
                    $failReason = [];

                    $target = Member::where('username_kana_s', $kana_s)
                        ->where('username_kana_m', $kana_m)
                        ->first();

                    if (!$target) {
                        $failReason[] = '氏名';
                    } else {
                        if ((int)$target->sex !== $sex) {
                            $failReason[] = '性別';
                        }
                        if ($target->birthday !== $fullBirthday) {
                            $failReason[] = '生年月日';
                        }
                    }

                    Log::info("不一致データ：kana_s={$kana_s}, kana_m={$kana_m}, sex={$sex}, birthday={$fullBirthday} → 不一致項目: " . implode('・', $failReason));

                    $unmatchedRecords[] = [
                        'username_kana_s' => $r->username_kana_s,
                        'username_kana_m' => $r->username_kana_m,
                        'sex'             => $r->sex,
                        'birthday1'       => $r->birthday1,
                        'birthday2'       => $r->birthday2,
                        'birthday3'       => $r->birthday3,
                        '不一致カラム'       => implode('・', $failReason) ?: '全体不一致',
                    ];
                }
            }

        Log::info(" 更新件数: {$updatedCount}, 不一致: " . count($unmatchedRecords));

        // Step 3: CSV出力
        if (count($unmatchedRecords) > 0) {
            $csvHeader = ['username_kana_s', 'username_kana_m', 'sex', 'birthday1', 'birthday2', 'birthday3', '不一致カラム'];

            $callback = function () use ($unmatchedRecords, $csvHeader) {
                $stream = fopen('php://output', 'w');
                fputcsv($stream, $csvHeader);
                foreach ($unmatchedRecords as $row) {
                    fputcsv($stream, $row);
                }
                fclose($stream);
            };

            return response()->streamDownload($callback, 'unmatched_rankup.csv', [
                'Content-Type' => 'text/csv',
            ]);
        }
    }

    public function downloadUnmatched()
    {
        $filePath = storage_path('app/rankup/unmatched_rankup.csv');
        if (!file_exists($filePath)) {
            abort(404, 'CSVファイルが存在しません。');
        }

        return response()->download($filePath, 'unmatched_rankup.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function deleteAll(Request $request)
    {
        $mode = $request->input('mode', 'unprocessed'); // デフォルト: 未処理のみ

        if ($mode === 'all') {
            \App\Models\Rankup::truncate(); // 全削除（IDリセット含む）
            Log::info(" 全ての年度更新インポートデータを削除しました。");

            return response()->json([
                'message' => '全てのインポートデータを削除しました。',
            ]);
        } elseif ($mode === 'unprocessed') {
            \App\Models\Rankup::where('rankup_flg', 0)->delete();
            Log::info(" 未処理の年度更新インポートデータのみ削除しました。");

            return response()->json([
                'message' => '未処理のインポートデータを削除しました。',
            ]);
        } else {
            return response()->json([
                'message' => '無効な削除モードが指定されました。',
            ], 400);
        }
    }

    public function unmatchedMembers()
    {
        $unmatched = Member::from('t_members')
        ->select(
            't_members.grade_category',
            't_members.username_kana_s',
            't_members.username_kana_m',
            't_members.sex',
            't_members.birthday'
        )
        ->leftJoin('rankups_table', function ($join) {
            $join->on('t_members.username_kana_s', '=', DB::raw("TRIM(rankups_table.username_kana_s)"))
                ->on('t_members.username_kana_m', '=', DB::raw("TRIM(rankups_table.username_kana_m)"))
                ->on('t_members.sex', '=', DB::raw("CASE rankups_table.sex WHEN '男' THEN 1 WHEN '女' THEN 2 ELSE NULL END"))
                ->on('t_members.birthday', '=', DB::raw("STR_TO_DATE(CONCAT(rankups_table.birthday1, '-', rankups_table.birthday2, '-', rankups_table.birthday3), '%Y-%m-%d')"));
        })
        ->whereNull('rankups_table.id')
        ->get();

        $csvHeader = ['学年カテゴリ', '姓(かな)', '名(かな)', '性別', '生年月日'];

        $callback = function () use ($unmatched, $csvHeader) {
            $stream = fopen('php://output', 'w');
            fputcsv($stream, $csvHeader);

            foreach ($unmatched as $row) {
                fputcsv($stream, [
                    $this->getGradeCategoryLabel($row->grade_category),
                    $row->username_kana_s,
                    $row->username_kana_m,
                    $this->getSexLabel($row->sex),
                    $row->birthday,
                ]);
            }
            fclose($stream);
        };

        return response()->streamDownload($callback, 'unmatched_members.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    private function getGradeCategoryLabel($value)
    {
        return config('labels.grade_categories')[(int)$value] ?? '-';
    }

    private function getSexLabel($value)
    {
        return config('labels.sexes')[(int)$value] ?? '-';
    }
    
}