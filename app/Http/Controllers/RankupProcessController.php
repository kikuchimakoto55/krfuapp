<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Rankup;
use App\Models\Member;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
            $fullBirthday = sprintf('%04d-%02d-%02d', $r->birthday1, $r->birthday2, $r->birthday3);

            $member = Member::where('username_kana_s', $r->username_kana_s)
                ->where('username_kana_m', $r->username_kana_m)
                ->where('sex', $r->sex)
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
                $target = Member::where('username_kana_s', $r->username_kana_s)
                    ->where('username_kana_m', $r->username_kana_m)
                    ->first();

                if (!$target) {
                    $failReason[] = '氏名';
                } elseif ($target->sex !== $r->sex) {
                    $failReason[] = '性別';
                } elseif ($target->birthday !== $fullBirthday) {
                    $failReason[] = '生年月日';
                }

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
            return new StreamedResponse(function () use ($unmatchedRecords) {
                $stream = fopen('php://output', 'w');
                fputcsv($stream, ['username_kana_s', 'username_kana_m', 'sex', 'birthday1', 'birthday2', 'birthday3', '不一致カラム']);
                foreach ($unmatchedRecords as $row) {
                    fputcsv($stream, $row);
                }
                fclose($stream);
            }, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="unmatched_rankup.csv"',
            ]);
        }

        return response()->json([
            'message' => '年度更新完了',
            'updated' => $updatedCount,
            'unmatched' => 0,
        ]);
    }
}