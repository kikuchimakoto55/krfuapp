<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\Member;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class MemberImportFromContactController extends Controller
{
    public function import(Request $request): JsonResponse
    {
        if (!$request->hasFile('file')) {
            return response()->json(['message' => 'CSVファイルがアップロードされていません。'], 400);
        }

        $file = $request->file('file');
        if (!$file->isValid() || strtolower($file->getClientOriginalExtension()) !== 'csv') {
            return response()->json(['message' => 'CSV形式のファイルを指定してください。'], 400);
        }

        $handle = fopen($file->getRealPath(), 'r');
        if (!$handle) {
            return response()->json(['message' => 'CSVファイルを開けませんでした。'], 500);
        }

        $header = fgetcsv($handle);
        if ($header === false) {
            fclose($handle);
            return response()->json(['message' => 'CSVファイルが空です。'], 400);
        }

        $header = array_map(fn($h) => trim(mb_convert_kana($h, 'as')), $header);
        $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]);

        $idx = fn($col) => array_search($col, $header, true);
        $map = [
            'grade_category'       => $idx('Select 9'),
            'username_sei'         => $idx('Text 993'),
            'username_mei'         => $idx('Text 103'),
            'username_kana_s'      => $idx('Text 954'),
            'username_kana_m'      => $idx('Text 402'),
            'username_en_s'        => $idx('Text 980'),
            'username_en_m'        => $idx('Text 618'),
            'sex'                  => $idx('Checkbox 69'),
            'birth_year'           => $idx('Select 227'),
            'birth_month'          => $idx('Select 21'),
            'birth_day'            => $idx('Select 130'),
            'height'               => $idx('Number 632'),
            'weight'               => $idx('Number 573'),
            'blood_type'           => $idx('Select 116'),
            'zip'                  => $idx('Zipcode'),
            'address'              => $idx('Address'),
            'enrolled_school'      => $idx('Text 285'),
            'guardian_name'        => $idx('Text 478'),
            'guardian_email'       => $idx('Email 413'),
            'guardian_tel'         => $idx('Tel 615'),
            'relationship'         => $idx('Select 45'),
            'emergency_name1'      => $idx('Text 411'),
            'emergency_email1'     => $idx('Email 255'),
            'emergency_tel1'       => $idx('Tel 488'),
            'email'                => $idx('Email 566'),
            'tel'                  => $idx('Tel 348'),
            'remarks'              => $idx('Textarea 686'),
        ];
        
        Log::debug('username_en_s index:', ['index' => $map['username_en_s']]);
        foreach (['username_en_s', 'username_kana_s', 'username_kana_m'] as $key) {
            if ($map[$key] === false) {
                Log::error("CSVのヘッダに必要な列 '{$key}' が存在しません。");
                return response()->json(['message' => "CSVに必要な列 '{$key}' がありません。"], 400);
            }
        }

        $successCount = 0;
        $skipCount = 0;
        $successIds = [];
        $errors = [];
        $lineNumber = 2;

        while (($row = fgetcsv($handle)) !== false) {
            if ($row === [null] || count($row) === 0 || (count($row) === 1 && $row[0] === null)) {
                $lineNumber++;
                continue;
            }

            $kanaS = $this->nullIfEmpty($row[$map['username_kana_s']] ?? null);
            $kanaM = $this->nullIfEmpty($row[$map['username_kana_m']] ?? null);
            $sex   = $this->nullIfEmpty($row[$map['sex']] ?? null);
            $sexValue = $sex === '男' ? 1 : ($sex === '女' ? 2 : null);

            $year  = $row[$map['birth_year']] ?? null;
            $month = $row[$map['birth_month']] ?? null;
            $day   = $row[$map['birth_day']] ?? null;

            if (!in_array($sex, ['男', '女'], true)) {
                $errors[] = ["line" => $lineNumber, "reason" => "性別が不正"];
                $skipCount++; $lineNumber++; continue;
            }

            if (!ctype_digit($year) || !ctype_digit($month) || !ctype_digit($day)) {
                $errors[] = ["line" => $lineNumber, "reason" => "生年月日が不正"];
                $skipCount++; $lineNumber++; continue;
            }

            try {
                $birthday = Carbon::createFromDate((int)$year, (int)$month, (int)$day);
            } catch (\Exception $e) {
                $errors[] = ["line" => $lineNumber, "reason" => "無効な日付"];
                $skipCount++; $lineNumber++; continue;
            }

            $addressRaw = $row[$map['address']] ?? null;
            [$address1, $address2] = $this->splitAddress($addressRaw);

            try {
                Log::debug("行 {$lineNumber} の username_en_s 値: ", [
                    'raw' => $row[$map['username_en_s']] ?? null,
                    'nullIfEmpty' => $this->nullIfEmpty($row[$map['username_en_s']] ?? null),
                ]);

                $gradeMap = [
                    '年年少' => 1,
                    '年少' => 2,
                    '年中' => 3,
                    '年長' => 4,
                    '小学1年生' => 5,
                    '小学2年生' => 6,
                    '小学3年生' => 7,
                    '小学4年生' => 8,
                    '小学5年生' => 9,
                    '小学6年生' => 10,
                    '中学1年生' => 11,
                    '中学2年生' => 12,
                    '中学3年生' => 13,
                ];
                $gradeRaw = $row[$map['grade_category']] ?? '';
                $gradeValue = $gradeMap[$gradeRaw] ?? null;

                $bloodMap = [
                    'A'  => 1,
                    'B'  => 2,
                    'AB' => 3,
                    'O'  => 4,
                    'その他'  => 5,
                ];
                $bloodRaw = $row[$map['blood_type']] ?? '';
                $bloodValue = $bloodMap[$bloodRaw] ?? null;

                $relationshipMap = [
                    '父' => 1,
                    '母' => 2,
                    '祖父' => 3,
                    '祖母' => 4,
                    'その他' => 5,
                    '本人' => 6,
                ];
                $relationshipRaw = $row[$map['relationship']] ?? '';
                $relationshipValue = $relationshipMap[$relationshipRaw] ?? null;

                

                $member = Member::updateOrCreate(
                    [
                        'username_kana_s' => $kanaS,
                        'username_kana_m' => $kanaM,
                        'birthday' => $birthday->format('Y-m-d'),
                        'sex' => $sexValue,
                    ],
                    [
                        'sex'                => $sexValue,
                        'grade_category'     => $gradeValue,
                        'username_sei'       => $this->nullIfEmpty($row[$map['username_sei']] ?? null),
                        'username_mei'       => $this->nullIfEmpty($row[$map['username_mei']] ?? null),
                        'username_en_s'      => $this->nullIfEmpty($row[$map['username_en_s']] ?? null),
                        'username_en_m'      => $this->nullIfEmpty($row[$map['username_en_m']] ?? null),
                        'height'             => is_numeric($row[$map['height']] ?? null) ? (int)$row[$map['height']] : null,
                        'weight'             => is_numeric($row[$map['weight']] ?? null) ? (int)$row[$map['weight']] : null,
                        'blood_type'         => $bloodValue,
                        'zip'                => $this->nullIfEmpty($row[$map['zip']] ?? null),
                        'address1'           => $address1,
                        'address2'           => $address2,
                        'enrolled_school'    => $this->nullIfEmpty($row[$map['enrolled_school']] ?? null),
                        'guardian_name'      => $this->nullIfEmpty($row[$map['guardian_name']] ?? null),
                        'guardian_email'     => $this->nullIfEmpty($row[$map['guardian_email']] ?? null),
                        'guardian_tel'       => $this->nullIfEmpty($row[$map['guardian_tel']] ?? null),
                        'relationship'       => $relationshipValue,
                        'emergency_name1'    => $this->nullIfEmpty($row[$map['emergency_name1']] ?? null),
                        'emergency_email1'   => $this->nullIfEmpty($row[$map['emergency_email1']] ?? null),
                        'emergency_tel1'     => $this->nullIfEmpty($row[$map['emergency_tel1']] ?? null),
                        'email'              => $this->nullIfEmpty($row[$map['email']] ?? null),
                        'tel'                => $this->nullIfEmpty($row[$map['tel']] ?? null),
                        'remarks'            => $this->nullIfEmpty($row[$map['remarks']] ?? null),
                        'classification'     => 4,
                        'membershipfee_conf' => null,
                        'association_id'     => null,
                        'status'             => 1,
                        'graduation_year'    => null,
                        'password'           => Hash::make('import_default'),// 任意の仮パスワード
                        'authoritykinds_id'  => 4,
                        'authoritykindsname' => '使用者権限',
                        'login_date'         => null,
                        'registration_date'  => now(),
                        'update_date'        => now(),
                        'coach_flg'          => 0,
                        'del_flg'            => 0,
                    ]
                );

                Log::info('保存完了', ['id' => $member->id, 'wasRecentlyCreated' => $member->wasRecentlyCreated]);
                $successIds[] = $member->id;
                $successCount++;
            } catch (\Exception $e) {
                Log::error('保存エラー', ['line' => $lineNumber, 'message' => $e->getMessage()]);
                $errors[] = ["line" => $lineNumber, "reason" => "DB保存エラー: " . $e->getMessage()];
                $skipCount++;
            }

            $lineNumber++;
        }

        fclose($handle);

        return response()->json([
            'success_count' => $successCount,
            'skip_count'    => $skipCount,
            'total_rows'    => $successCount + $skipCount,
            'success_ids'   => $successIds,
            'errors'        => $errors,
            'imported_at'   => now()->toDateTimeString(),
            'log_id'        => uniqid('import_', true),
        ]);
    }

    // 空文字を null に変換
    private function nullIfEmpty(?string $value): ?string
    {
        $value = trim($value ?? '');
        return $value === '' ? null : $value;
    }

    private function splitAddress(?string $input): array
    {
        if (!$input) return [null, null];
        $prefectures = [
            '北海道','青森県','岩手県','宮城県','秋田県','山形県','福島県',
            '茨城県','栃木県','群馬県','埼玉県','千葉県','東京都','神奈川県',
            '新潟県','富山県','石川県','福井県','山梨県','長野県',
            '岐阜県','静岡県','愛知県','三重県',
            '滋賀県','京都府','大阪府','兵庫県','奈良県','和歌山県',
            '鳥取県','島根県','岡山県','広島県','山口県',
            '徳島県','香川県','愛媛県','高知県',
            '福岡県','佐賀県','長崎県','熊本県','大分県','宮崎県','鹿児島県','沖縄県'
        ];

        foreach ($prefectures as $pref) {
            if (strpos($input, $pref) === 0) {
                $rest = trim(mb_substr($input, mb_strlen($pref)));
                return [$pref, $rest ?: null];
            }
        }

        return [$input, null];
    }
}
