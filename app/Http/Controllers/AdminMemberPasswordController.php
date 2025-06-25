<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminMemberPasswordController extends Controller
{
    public function change(Request $request, $id)
{
    // 管理者権限チェック
    if (Auth::user()->authoritykinds_id !== 1) {
        return response()->json(['error' => '権限がありません'], 403);
    }

    // バリデーション（confirmedを使うなら、password_confirmation も送信されている前提）
    $request->validate([
        'password' => 'required|string|min:6|confirmed',
    ]);

    // 対象ユーザー取得
    $member = Member::findOrFail($id);
    $member->password = Hash::make($request->password); 
    $member->save();

    return response()->json(['message' => 'パスワードを更新しました。']);
}
}