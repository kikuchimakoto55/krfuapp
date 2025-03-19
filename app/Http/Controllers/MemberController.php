<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        // 🔹 受信した検索条件を確認（デバッグ用）
        \Log::info('検索条件:', $request->all());

        // 🔹 検索クエリを開始（削除フラグが 0 のデータのみ取得）
        $query = Member::where('del_flg', 0);

        // 🔹 条件ごとにフィルタリング
        if ($request->filled('grade_category') && is_numeric($request->grade_category)) {
            $query->where('grade_category', intval($request->grade_category));
        }
        if ($request->filled('username_sei')) {
            $query->where('username_sei', 'like', '%' . $request->username_sei . '%');
        }
        if ($request->filled('username_mei')) {
            $query->where('username_mei', 'like', '%' . $request->username_mei . '%');
        }
        if ($request->filled('username_kana_s')) {
            $query->where('username_kana_s', 'like', '%' . $request->username_kana_s . '%');
        }
        if ($request->filled('username_kana_m')) {
            $query->where('username_kana_m', 'like', '%' . $request->username_kana_m . '%');
        }
        if ($request->filled('birthday')) {
            $query->whereDate('birthday', $request->birthday);
        }
        if ($request->filled('address1')) {
            $query->where('address1', 'like', '%' . $request->address1 . '%');
        }
        if ($request->filled('address2')) {
            $query->where('address2', 'like', '%' . $request->address2 . '%');
        }
        if ($request->filled('guardian_name')) {
            $query->where('guardian_name', 'like', '%' . $request->guardian_name . '%');
        }
        if ($request->filled('guardian_email')) {
            $query->where('guardian_email', 'like', '%' . $request->guardian_email . '%');
        }
        if ($request->filled('guardian_tel') && is_numeric($request->guardian_tel)) {
            $query->where('guardian_tel', $request->guardian_tel);
        }
        if ($request->filled('registration_date')) {
            $query->whereDate('registration_date', $request->registration_date);
        }
        if ($request->filled('classification') && is_numeric($request->classification)) {
            $query->where('classification', intval($request->classification));
        }
        if ($request->filled('status') && is_numeric($request->status)) {
            $query->where('status', intval($request->status));
        }
        if ($request->filled('graduation_year') && is_numeric($request->graduation_year)) {
            $query->whereYear('graduation_year', intval($request->graduation_year));
        }
        if ($request->filled('coach_flg') && is_numeric($request->coach_flg)) {
            $query->where('coach_flg', intval($request->coach_flg));
        }

        // 🔹 新しく追加した検索条件
        if ($request->filled('emergency_name1')) {
            $query->where('emergency_name1', 'like', '%' . $request->emergency_name1 . '%');
        }
        if ($request->filled('emergency_email1')) {
            $query->where('emergency_email1', 'like', '%' . $request->emergency_email1 . '%');
        }
        if ($request->filled('emergency_tel1') && is_numeric($request->emergency_tel1)) {
            $query->where('emergency_tel1', $request->emergency_tel1);
        }
        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . $request->email . '%');
        }
        if ($request->filled('tel') && is_numeric($request->tel)) {
            $query->where('tel', $request->tel); // 完全一致
        }
        if ($request->filled('membershipfee_conf') && is_numeric($request->membershipfee_conf)) {
            $query->where('membershipfee_conf', intval($request->membershipfee_conf));
        }

        // 🔹 ページネーションを適用（1ページ10件）
        $members = $query->paginate(10);

        return response()->json($members);
    }
    /**
     * 🔹 会員登録処理（バリデーション適用）
     */
    public function store(Request $request)
    {
        // 🔹 入力バリデーション
        $validated = $request->validate([
            'username_sei' => 'nullable|string|max:15',
            'username_mei' => 'nullable|string|max:15',
            'guardian_tel' => 'nullable|digits_between:8,11',
            'email' => 'nullable|email|max:100',
            'graduation_year' => 'nullable|digits:4',
        ]);

        // 🔹 新しい会員データを作成
        $member = Member::create($validated);

        return response()->json([
            'message' => '会員登録が完了しました',
            'data' => $member
        ], 201);
    }
}
