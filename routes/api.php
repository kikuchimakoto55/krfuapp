<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Member; // t_members 用のモデル

// Sanctum の CSRF Cookie を取得
Route::get('/sanctum/csrf-cookie', function (Request $request) {
    return response()->noContent(); // CSRF Cookie をセット
});

// ログイン処理
Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $member = Member::where('email', $credentials['email'])->first();

    if (!$member || !Hash::check($credentials['password'], $member->password)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    // トークン作成
    $token = $member->createToken('authToken')->plainTextToken;

    return response()->json([
        'token' => $token,
        'user' => [
            'id' => $member->id,
            'email' => $member->email,
            'authoritykinds_id' => $member->authoritykinds_id,
            'authoritykindsname' => $member->authoritykindsname
        ]
    ]);
});

// ログアウト処理
Route::middleware('auth:sanctum')->post('/logout', function (Request $request) {
    $user = $request->user();

    if ($user) {
        $user->tokens()->delete();
    }

    // CSRF Cookie を削除
    return response()->json(['message' => 'ログアウトしました'])->cookie('XSRF-TOKEN', '', -1);
});

// 認証済みユーザー情報を取得
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return response()->json([
        'id' => $request->user()->id,
        'email' => $request->user()->email,
        'authoritykinds_id' => $request->user()->authoritykinds_id,
        'authoritykindsname' => $request->user()->authoritykindsname
    ]);
});

// CORS の問題を防ぐための OPTIONS メソッド対応
Route::options('/{any}', function () {
    return response()->noContent();
})->where('any', '.*');

// パスワード変更 API (オプション)
Route::middleware('auth:sanctum')->post('/change-password', function (Request $request) {
    $request->validate([
        'current_password' => 'required',
        'new_password' => 'required|min:8',
    ]);

    $user = $request->user();

    // 現在のパスワードが正しいかチェック
    if (!Hash::check($request->current_password, $user->password)) {
        return response()->json(['message' => '現在のパスワードが間違っています'], 403);
    }

    // 新しいパスワードを設定
    $user->password = Hash::make($request->new_password);
    $user->save();

    return response()->json(['message' => 'パスワードを変更しました']);
});
