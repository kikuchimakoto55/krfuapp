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
        if ($request->filled('grade_category')) {
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
        if ($request->filled('guardian_tel')) {
            $query->where('guardian_tel', $request->guardian_tel);
        }
        if ($request->filled('registration_date')) {
            $query->whereDate('registration_date', $request->registration_date);
        }
        if ($request->filled('classification')) {
            $query->where('classification', intval($request->classification));
        }
        if ($request->filled('status')) {
            $query->where('status', intval($request->status));
        }
        if ($request->filled('graduation_year')) {
            $query->whereYear('graduation_year', $request->graduation_year);
        }
        if ($request->filled('coach_flg')) {
            $query->where('coach_flg', intval($request->coach_flg));
        }

        // 🔹 検索結果を取得
        $members = $query->get();

        return response()->json($members);
    }
}
