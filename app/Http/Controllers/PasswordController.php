<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class PasswordController extends Controller
{
    public function change(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'new_password' => ['required', 'min:8'],
        ]);

        $user = Auth::user();

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
