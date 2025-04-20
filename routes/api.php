<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Member; // t_members 用のモデル
use App\Http\Controllers\MemberController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\FamilyController;
use App\Http\Controllers\TournamentController;


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
            'member_id' => $member->member_id,
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
        $user->tokens()->delete(); // ← トークンを削除
    }

    // 🔴 LaravelセッションとCSRF Cookieの両方を無効化して返す
    return response()->json(['message' => 'ログアウトしました'])
        ->cookie('XSRF-TOKEN', '', -1)
        ->cookie('laravel_session', '', -1);
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

// 一覧取得はログインが必要（そのままでOK）
Route::middleware(['auth:sanctum'])->get('/members', [MemberController::class, 'index']);

// 🔓 会員登録はログインなしでもOKにする
Route::post('/members', [MemberController::class, 'store']);

//家族登録モーダルルーティン
Route::get('/members/search', [MemberController::class, 'search']);

// POST /api/change-password に対応する処理（現パスワードチェック、更新）を追加
//マイページルーティング
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/members/{id}', [MemberController::class, 'show']);
    Route::put('/members/{id}', [MemberController::class, 'update']);
    Route::delete('/members/{id}', [MemberController::class, 'destroy']);
    Route::post('/change-password', [PasswordController::class, 'change']);
    Route::get('/teams', [App\Http\Controllers\TeamController::class, 'index']);
    Route::post('/teams', [App\Http\Controllers\TeamController::class, 'store']);
    Route::get('/teams/{id}', [App\Http\Controllers\TeamController::class, 'show']);
    Route::put('/teams/{id}', [App\Http\Controllers\TeamController::class, 'update']);
    Route::delete('/teams/{id}', [App\Http\Controllers\TeamController::class, 'destroy']);

});

//家族管理ルーティング
Route::middleware('auth:sanctum')->post('/families', [FamilyController::class, 'store']);

//家族編集ルーティング
Route::middleware('auth:sanctum')->put('/families/{id}', [FamilyController::class, 'update']);
//家族解除ルーティング
Route::middleware('auth:sanctum')->delete('/families/{id}', [FamilyController::class, 'destroy']);
// 家族検索ルーティング
Route::middleware('auth:sanctum')->get('/families/search', [FamilyController::class, 'search']);
// 大会登録ルーティング
Route::post('/tournaments', [TournamentController::class, 'store']);
//大会一覧ルーティング
Route::get('/tournaments', [TournamentController::class, 'index']);

//大会編集ルーティング
Route::get('/tournaments/{id}', [TournamentController::class, 'show']);
Route::put('/tournaments/{id}', [TournamentController::class, 'update']);

//会員登録完了画面情報出力ルーティング
Route::get('/members/{id}/public', [MemberController::class, 'public']);
