<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Member;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\InitialPasswordController;
use App\Http\Controllers\FamilyController;
use App\Http\Controllers\TournamentController;
use App\Http\Controllers\TournamentResultController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\VenueController;
use App\Http\Controllers\LicenseController;
use App\Http\Controllers\HCredentialController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\MemberImportController;
use App\Http\Controllers\MemberImportFromContactController;
use App\Http\Controllers\MemberExportController;
use App\Http\Controllers\AdminMemberPasswordController;
use App\Http\Controllers\TeamsImportController;
use App\Http\Controllers\TeamController;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\RankupImportController;
use App\Http\Controllers\RankupProcessController;


// CSRF Cookie
Route::get('/sanctum/csrf-cookie', function (Request $request) {
    return response()->noContent();
});

// ログイン
Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    Log::debug('ログイン試行', ['email' => $credentials['email']]);

    $member = Member::where('email', $credentials['email'])
        ->where('status', 1)
        ->where('del_flg', 0)
        ->first();

    if (!$member || !Hash::check($credentials['password'], $member->password)) {
        Log::warning('ログイン失敗', ['email' => $credentials['email']]);
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    Log::info('ログイン成功', ['member_id' => $member->member_id]);

    $token = $member->createToken('authToken')->plainTextToken;
    //  権限種別が使用者権限ユーザーチェック
    $isResetRequired = (
        $member->authoritykinds_id === 4 &&
        is_null($member->login_date)
    );

    return response()->json([
        'token' => $token,
        'user' => [
            'member_id' => $member->member_id,
            'email' => $member->email,
            'authoritykinds_id' => $member->authoritykinds_id,
            'authoritykindsname' => $member->authoritykindsname,
        ],
        'reset_required' => $isResetRequired, //  フラグをここで追加
    ]);
});

// ログアウト
Route::middleware('auth:sanctum')->post('/logout', function (Request $request) {
    $user = $request->user();
    if ($user) {
        $user->tokens()->delete();
    }
    return response()->json(['message' => 'ログアウトしました'])
        ->cookie('XSRF-TOKEN', '', -1)
        ->cookie('laravel_session', '', -1);
});

// 認証済みユーザー情報取得
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return response()->json([
        'id' => $request->user()->id,
        'email' => $request->user()->email,
        'authoritykinds_id' => $request->user()->authoritykinds_id,
        'authoritykindsname' => $request->user()->authoritykindsname
    ]);
});

// OPTIONS 対応
Route::options('/{any}', function () {
    return response('', 204, [
        'Access-Control-Allow-Origin' => 'http://localhost:5173',
        'Access-Control-Allow-Credentials' => 'true',
        'Access-Control-Allow-Methods' => 'POST, GET, OPTIONS, PUT, DELETE',
        'Access-Control-Allow-Headers' => 'Content-Type, Authorization, X-Requested-With',
    ]);
})->where('any', '.*');

// 認証必要なルート
Route::middleware(['auth:sanctum'])->group(function () {
    Route::put('/admin/members/{id}/password', [AdminMemberPasswordController::class, 'change']);
    Route::post('/admin/members/{id}/password', [AdminMemberPasswordController::class, 'change']);
    

    Route::get('/members/export', [MemberExportController::class, 'export']); //  ここを先に
    Route::post('/members/import-from-contact', [MemberImportFromContactController::class, 'import']);
    Route::get('/members', [MemberController::class, 'index']);
    Route::get('/members/search', [MemberController::class, 'search']);
    Route::get('/members/{id}', [MemberController::class, 'show']);
    Route::put('/members/{id}', [MemberController::class, 'update']);
    Route::delete('/members/{id}', [MemberController::class, 'destroy']);
    Route::post('/members/import-preview', [MemberImportController::class, 'preview']);
    Route::post('/members/import', [MemberImportController::class, 'import']);

    Route::post('/families', [FamilyController::class, 'store']);
    Route::get('/families/search', [FamilyController::class, 'search']);
    Route::delete('/families/reverse', [FamilyController::class, 'deleteReverse']);
    Route::post('/change-password', [PasswordController::class, 'change']);
    Route::put('/members/{id}/password-initial', [InitialPasswordController::class, 'change']);

    Route::get('/teams', [TeamController::class, 'index']);
    Route::post('/teams', [TeamController::class, 'store']);
    Route::get('/teams/export', [TeamsImportController::class, 'export']);
    Route::get('/teams/{id}', [TeamController::class, 'show']);
    Route::put('/teams/{id}', [TeamController::class, 'update']);
    Route::delete('/teams/{id}', [TeamController::class, 'destroy']);
    Route::post('/teams/import-preview', [TeamsImportController::class, 'preview']);
    Route::post('/teams/import', [TeamsImportController::class, 'import']);

    

    Route::post('/tournament-results', [TournamentResultController::class, 'store']);
    Route::get('/tournament-results', [TournamentResultController::class, 'index']);
    Route::delete('/tournament-results/by-tournament/{tournament_id}', [TournamentResultController::class, 'destroyByTournamentId']);
    Route::put('/tournament-results/{tournament_id}', [TournamentResultController::class, 'update']);
    Route::get('/tournament-results/{id}', [TournamentResultController::class, 'show']);
    Route::put('/tournament-results/update-by-tournament/{tournament_id}', [TournamentResultController::class, 'updateByTournament']);
    Route::delete('/tournaments/{id}', [TournamentController::class, 'destroy']);

    Route::get('/games', [GameController::class, 'index']);
    Route::post('/games', [GameController::class, 'store']);
    Route::get('/games/search', [GameController::class, 'search']);
    Route::get('/games/{id}', [GameController::class, 'show']);
    Route::put('/games/{id}', [GameController::class, 'update']);
    Route::delete('/games/{id}', [GameController::class, 'destroy']);

    Route::get('/venues', [VenueController::class, 'index']);
    Route::post('/venues', [VenueController::class, 'store']);
    Route::get('/venues/{id}', [VenueController::class, 'show']);
    Route::put('/venues/{id}', [VenueController::class, 'update']);
    Route::delete('/venues/{id}', [VenueController::class, 'destroy']);

    Route::get('/tournaments/search', [TournamentController::class, 'search']);
    Route::get('/tournaments/list', [TournamentController::class, 'list']);
    Route::get('/tournaments/{id}/check-division', [TournamentController::class, 'checkDivisionFlg']);
    Route::get('/tournaments/{id}/divisions', [TournamentController::class, 'divisions']);

    Route::get('/licenses', [LicenseController::class, 'index']);
    Route::post('/licenses', [LicenseController::class, 'store']);
    Route::get('/licenses/{id}', [LicenseController::class, 'show']);
    Route::put('/licenses/{id}', [LicenseController::class, 'update']);
    Route::delete('/licenses/{id}', [LicenseController::class, 'destroy']);

    Route::get('/members/{id}/credentials', [HCredentialController::class, 'getForMember']);
    Route::post('/members/{id}/credentials', [HCredentialController::class, 'updateForMember']);
    Route::put('/members/{id}/credentials', [HCredentialController::class, 'updateForMember']);

    Route::get('/events', [EventController::class, 'index']);
    Route::post('/events', [EventController::class, 'store']);
    Route::get('/events/{id}', [EventController::class, 'show']);
    Route::put('/events/{id}', [EventController::class, 'update']);
    Route::delete('/events/{id}', [EventController::class, 'destroy']);

    Route::post('/rankup/import', [RankupImportController::class, 'import']);
    Route::post('/rankup/process', [RankupProcessController::class, 'process']);
    Route::get('/rankup/list', [RankupImportController::class, 'list']);
    Route::get('/rankup/download-unmatched', [RankupProcessController::class, 'downloadUnmatched']);
});

// ログイン不要で使えるルート
Route::post('/members', [MemberController::class, 'store']);
Route::post('/tournaments', [TournamentController::class, 'store']);
Route::get('/tournaments', [TournamentController::class, 'index']);
Route::get('/tournaments/{id}', [TournamentController::class, 'show']);
Route::put('/tournaments/{id}', [TournamentController::class, 'update']);
Route::get('/members/{id}/public', [MemberController::class, 'public']);
Route::get('/tournament-results/{id}/exists', [TournamentResultController::class, 'exists']);
