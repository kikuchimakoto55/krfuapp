<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MemberExportController extends Controller
{
    public function export()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="members_export.csv"',
        ];

        // CSV出力に使うカラム（DB用キー）
        $columns = [
            'grade_category',
            'username_sei',
            'username_mei',
            'username_kana_s',
            'username_kana_m',
            'username_en_s',
            'username_en_m',
            'sex',
            'birthday',
            'height',
            'weight',
            'blood_type',
            'zip',
            'address1',
            'address2',
            'address3',
            'enrolled_school',
            'guardian_name',
            'guardian_email',
            'guardian_tel',
            'relationship',
            'emergency_name1',
            'emergency_email1',
            'emergency_tel1',
            'email',
            'tel',
            'remarks',
            'registration_date',
            'classification',
            'membershipfee_conf',
            'association_id',
            'status',
            'graduation_year',
            'authoritykinds_id',
            'authoritykindsname',
            'coach_flg',
        ];

        // CSV1行目（見出しに表示する日本語）
        $headersRow = [
            '学年カテゴリ', '姓', '名', '姓カナ', '名カナ',
            '姓英語', '名英語', '性別', '生年月日', '身長',
            '体重', '血液型', '郵便番号', '住所１', '住所２', '住所３',
            '在籍学校名', '保護者氏名', '保護者メール', '保護者電話番号',
            '続柄', '緊急連絡先氏名', '緊急連絡先メール', '緊急連絡先電話',
            '本人メール', '本人電話', '備考', '登録日', '所属区分',
            '保険登録番号', '協会ID', '在籍状況', '卒業年度',
            '権限ID', '権限名', '指導員フラグ',
        ];

        $callback = function () use ($columns, $headersRow) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF"); // UTF-8 BOM（Excel対策）

            fputcsv($handle, $headersRow); // ← ここで日本語ヘッダーを出力

            Member::where('del_flg', 0)->chunk(100, function ($members) use ($handle, $columns) {
                foreach ($members as $member) {
                    $row = [];
                    foreach ($columns as $col) {
                        $row[] = $member->$col ?? '';
                    }
                    fputcsv($handle, $row);
                }
            });

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
