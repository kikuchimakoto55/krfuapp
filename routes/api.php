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
use App\Http\Controllers\TournamentResultController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\VenueController;


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

    //  LaravelセッションとCSRF Cookieの両方を無効化して返す
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

//  会員登録はログインなしでもOKにする
Route::post('/members', [MemberController::class, 'store']);

//家族登録モーダルルーティン
Route::get('/members/search', [MemberController::class, 'search']);


// POST /api/change-password に対応する処理（現パスワードチェック、更新）を追加
//マイページルーティング
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/members/{id}', [MemberController::class, 'show']);//会員詳細
    Route::put('/members/{id}', [MemberController::class, 'update']);//会員詳細更新
    Route::delete('/members/{id}', [MemberController::class, 'destroy']);//会員削除
    Route::post('/change-password', [PasswordController::class, 'change']);//パスワード更新
    Route::get('/teams', [App\Http\Controllers\TeamController::class, 'index']);//チーム一覧
    Route::post('/teams', [App\Http\Controllers\TeamController::class, 'store']);//チーム登録
    Route::get('/teams/{id}', [App\Http\Controllers\TeamController::class, 'show']);//チーム詳細
    Route::put('/teams/{id}', [App\Http\Controllers\TeamController::class, 'update']);//チーム更新
    Route::delete('/teams/{id}', [App\Http\Controllers\TeamController::class, 'destroy']);//チーム削除
    Route::post('/tournament-results', [TournamentResultController::class, 'store']);//大会結果登録
    Route::get('/tournament-results', [TournamentResultController::class, 'index']);//大会結果詳細
    Route::delete('/tournaments/{id}', [TournamentController::class, 'destroy']);//大会削除
    Route::get('/games', [GameController::class, 'index']); // 試合一覧
    Route::post('/games', [GameController::class, 'store']); // 試合登録
    Route::get('/games/search', [GameController::class, 'search']);//試合検索
    Route::get('/games/{id}', [GameController::class, 'show']); // 試合詳細
    Route::put('/games/{id}', [GameController::class, 'update']); // 試合更新
    Route::delete('/games/{id}', [GameController::class, 'destroy']); // 試合削除
    Route::get('/venues', [VenueController::class, 'index']);// 会場一覧
    Route::post('/venues', [VenueController::class, 'store']);// 会場管理
    Route::get('/venues/{id}', [VenueController::class, 'show']);//会場編集
    Route::delete('/venues/{id}', [VenueController::class, 'destroy']);//会場削除
    Route::get('/tournaments/search', [TournamentController::class, 'search']);
    Route::get('/tournaments/list', [TournamentController::class, 'list']);//試合登録前選択
    Route::get('/tournaments/{id}/check-division', [TournamentController::class, 'checkDivisionFlg']);//試合登録ディビジョン表示高速化
    Route::get('/tournaments/{id}/divisions', [TournamentController::class, 'divisions']);

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
//大会結果登録完了画面ルーティング
Route::middleware('auth:sanctum')->get('/tournament-results/{tournament_id}', [TournamentResultController::class, 'showByTournament']);

//大会結果編集（t_tournament_results の一括更新）
Route::middleware('auth:sanctum')->put('/tournament-results/update-by-tournament/{tournament_id}', [TournamentResultController::class, 'updateByTournament']);

//会場編集更新ルーティング
Route::middleware(['auth:sanctum'])->put('/venues/{id}', [VenueController::class, 'update']);

//試合検索ルーティング
Route::middleware(['auth:sanctum'])->get('/games/search', [GameController::class, 'search']);


