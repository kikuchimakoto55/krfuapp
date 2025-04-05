<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;
use App\Http\Requests\StoreMemberRequest;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UpdateMemberRequest;

class MemberController extends Controller
{
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
    $member = Member::find($id);

    if (!$member) {
        return response()->json(['message' => '会員が見つかりません'], 404);
    }

    return response()->json(['member' => $member]);
}

}
