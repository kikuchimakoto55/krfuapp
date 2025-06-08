<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class MemberImportController extends Controller
{
    // CSVプレビュー用（バリデーションのみ）
    public function preview(Request $request)
    {
        if (!$request->hasFile('file')) {
            return response()->json(['error' => 'ファイルがありません'], 422);
        }

        $file = $request->file('file');
        $rows = array_map('str_getcsv', file($file));
        $header = array_map('trim', array_shift($rows));

        $results = [];
        $errors = [];

        foreach ($rows as $index => $row) {
            $data = array_combine($header, $row);

            $validator = Validator::make($data, [
                'username_sei' => 'required|string|max:20',
                'username_mei' => 'required|string|max:20',
                'sex' => 'required|integer|in:1,2',
                'birthday' => 'required|date',
                'email' => 'nullable|email', // ← unique削除
                'password' => 'required|string|min:8',
            ]);

            if ($validator->fails()) {
                $formattedMessages = [];
                foreach ($validator->errors()->toArray() as $field => $msgs) {
                    foreach ($msgs as $msg) {
                        $value = isset($data[$field]) ? $data[$field] : '空';
                        $formattedMessages[] = $field . '（' . $value . '）: ' . $msg;
                    }
                }

                $errors[] = [
                    'row' => $index + 2,
                    'messages' => $formattedMessages,
                ];
            } else {
                $results[] = $data;
            }
        }

        return response()->json([
            'valid' => count($errors) === 0,
            'data' => $results,
            'errors' => $errors,
        ]);
    }

    // 実際の登録処理
    public function import(Request $request)
    {
        $records = $request->input('data');
        $now = now();

        foreach ($records as $data) {
            $match = [
                'username_kana_s' => $data['username_kana_s'],
                'username_kana_m' => $data['username_kana_m'],
                'birthday'        => $data['birthday'],
                'sex'             => $data['sex'],
            ];

            $existing = Member::where($match)->first();

            // 加工処理
            $data['password'] = Hash::make($data['password']);
            $data['zip'] = str_replace('-', '', $data['zip']);
            $data['del_flg'] = 0;
            $data['update_date'] = $now;

            if ($existing) {
                $existing->fill($data)->save();
            } else {
                $data['registration_date'] = $now;
                Member::create($data);
            }
        }

        return response()->json(['message' => 'インポート完了'], 200);
    }
}
