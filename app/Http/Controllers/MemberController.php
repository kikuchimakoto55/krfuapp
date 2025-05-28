<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;
use App\Http\Requests\StoreMemberRequest;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UpdateMemberRequest;
use Illuminate\Support\Facades\DB;

class MemberController extends Controller
{
     //家族登録モーダル
public function search(Request $request)
{
    $keyword = $request->input('keyword');

    if (!$keyword) {
        return response()->json(['data' => []]);
    }

    // 前後空白削除 & 全角→半角カタカナ→ひらがなへ変換（あれば）
    $normalizedKeyword = mb_convert_kana($keyword, 'c');

    $members = \DB::table('t_members')
        ->where(function ($query) use ($keyword) {
            $query->whereRaw("CONCAT(username_sei, username_mei) LIKE ?", ["%{$keyword}%"])
                  ->orWhereRaw("CONCAT(username_kana_s, username_kana_m) LIKE ?", ["%{$keyword}%"]);
        })
        ->select(
            'member_id', 'username_sei', 'username_mei',
            'username_kana_s', 'username_kana_m'
        )
        ->orderBy('username_sei')
        ->limit(50) // 結果数は適宜制限（任意）
        ->get();

    return response()->json(['data' => $members]);
}
    
    public function index(Request $request)
{
	// 受信した検索条件を確認（デバッグ用）
    \Log::info('検索条件:', $request->all());
	// 検索クエリを開始（削除フラグが 0 のデータのみ取得）
    $query = Member::where('del_flg', 0);

    // 数値型（完全一致）検索項目
    $intFields = [
        'grade_category', 'classification', 'status', 'graduation_year',
        'coach_flg', 'membershipfee_conf'
    ];
    foreach ($intFields as $field) {
        if ($request->filled($field) && is_numeric($request->$field)) {
            $query->where($field, intval($request->$field));
        }
    }

    // あいまい検索（like）対象フィールド
    $likeFields = [
        'username_sei', 'username_mei', 'username_kana_s', 'username_kana_m',
        'address1', 'address2', 'guardian_name', 'guardian_email',
        'emergency_name1', 'emergency_email1', 'email'
    ];
    foreach ($likeFields as $field) {
        if ($request->filled($field)) {
            $query->where($field, 'like', '%' . $request->$field . '%');
        }
    }

    // 日付・年月フィールド（正確にマッチ）
    if ($request->filled('birthday')) {
        $query->whereDate('birthday', $request->birthday);
    }
    if ($request->filled('registration_date')) {
        $query->whereDate('registration_date', $request->registration_date);
    }

    // 電話番号など完全一致
    $exactFields = ['guardian_tel', 'emergency_tel1', 'tel'];
    foreach ($exactFields as $field) {
        if ($request->filled($field)) {
            $query->where($field, $request->$field);
        }
    }

        // 🔹 並び順（IDの降順で安定化）
        $query->orderBy('member_id', 'desc');

        // 🔹 ページネーションを適用（1ページ10件）
        $members = $query->simplePaginate(10);
        return response()->json($members);
    }
    
    //会員登録処理（バリデーション適用）
    public function store(StoreMemberRequest $request)
{
    // 🔹 FormRequest（StoreMemberRequest）にてバリデーション済み
    $validated = $request->validated();

    // 🔐 パスワードをハッシュ化
    $validated['password'] = Hash::make($validated['password']);

    // 🔹 新しい会員データを作成
    $member = Member::create($validated);

    return response()->json(['message' => '登録完了', 'member' => $member], 201);
}

// 会員情報の取得（編集用）
public function edit($id)
{
    $member = Member::findOrFail($id);
    return response()->json($member);
}

// 会員情報の更新
public function update(UpdateMemberRequest $request, $id)
{
    $member = Member::findOrFail($id);
    $data = $request->all();

    // 🔐 パスワードが送信されていた場合のみ処理（空のときは無視）
    if ($request->filled('password')) {
        // 🔐 管理者チェック
        if (!auth()->check() || auth()->user()->authoritykinds_id !== 1) {
            return response()->json(['message' => 'パスワードの変更権限がありません'], 403);
        }

        // ハッシュ化してデータに含める
        $data['password'] = Hash::make($request->password);
    } else {
        // パスワードが空の場合は update 対象から除外
        unset($data['password']);
    }

    // 🔄 更新
    $member->update($data);

    return response()->json(['message' => '更新完了', 'member' => $member], 200);
}


// 会員詳細の取得（詳細画面表示用）
public function show($id)
{
    $member = Member::with(['hCredentials.license'])->find($id); // 保有資格と関連資格を取得

    if (!$member) {
        return response()->json(['message' => '会員が見つかりません'], 404);
    }

    // 家族情報を取得（片方向でOKな場合）
    $families = DB::table('t_families')
        ->join('t_members', 't_members.member_id', '=', 't_families.family_id')
        ->where('t_families.member_id', $id)
        ->select(
            't_families.id',
            't_members.member_id',
            't_members.username_sei',
            't_members.username_mei',
            't_families.relationship'
        )
        ->get();

    return response()->json([
        'member' => $member,
        'families' => $families,
        'h_credentials' => $member->hCredentials, // ← フロントと一致させる
    ]);
}

public function destroy($id)
{
    // 権限確認（管理者 or 運営のみ削除可）
    if (!auth()->check() || !in_array(auth()->user()->authoritykinds_id, [1, 2])) {
        return response()->json(['message' => '削除権限がありません'], 403);
    }

    $member = Member::findOrFail($id);
    $member->del_flg = 1; // 論理削除
    $member->save();

    return response()->json(['message' => '削除完了']);
}

// 会員登録完了画面でメールアドレス取得
public function public($id)
{
    $member = TMember::find($id);
    if (!$member) {
        return response()->json(['message' => '会員が見つかりません'], 404);
    }

    return response()->json([
        'email' => $member->email
    ]);
}

}