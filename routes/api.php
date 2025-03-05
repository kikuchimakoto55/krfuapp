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

// ログイン処理 (POST のみ)
Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $member = Member::where('email', $credentials['email'])->first();

    if (!$member || !Hash::check($credentials['password'], $member->password)) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    $token = $member->createToken('authToken')->plainTextToken;
    return response()->json(['token' => $token]);
});

// ログアウト処理

Route::middleware('auth:sanctum')->post('/logout', function (Request $request) {
    Auth::guard('web')->logout();
    $request->user()->tokens()->delete();

    return response()->json(['message' => 'ログアウトしました']);
});



// 認証済みユーザー情報を取得
Route::get('/user', function (Request $request) {
    return response()->json($request->user());
})->middleware('auth:sanctum');
