<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PasswordController extends Controller
{
    public function change(Request $request)
    {
        
        $request->validate([
            'current_password' => ['required'],
            'new_password' => ['required', 'min:8'],
        ]);

        $user = Auth::user();
        Log::debug('パスワード変更対象 user ID: ' . optional($user)->id);
        Log::debug('パスワード変更対象 email: ' . optional($user)->email);
        Log::debug('権限: ' . optional($user)->authoritykinds_id);
        Log::debug('現在のパスワード入力: ' . $request->current_password);
        Log::debug('DB上のハッシュ: ' . $user->password);
        Log::debug('Hash::check 判定: ' . (Hash::check($request->current_password, $user->password) ? 'OK' : 'NG'));

        // 現在のパスワードが一致するか確認
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => '現在のパスワードが正しくありません'], 422);
        }

        // 新しいパスワードを保存
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json(['message' => 'パスワードを変更しました']);
    }
}
