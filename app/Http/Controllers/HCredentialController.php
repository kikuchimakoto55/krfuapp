<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HCredential;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HCredentialController extends Controller
{
    /**
     * 指定した会員IDの保有資格を一括更新
     */
    public function updateForMember(Request $request, $id)
{
    $validated = $request->validate([
        'credentials' => 'required|array',
        'credentials.*.license_id' => 'required|integer|exists:t_licenses,license_id',
        'credentials.*.acquisition_date' => 'nullable|date',
        'credentials.*.expiration_date' => 'nullable|date',
        'credentials.*.valid_flg' => 'required|in:0,1',
        'credentials.*.h_credentials_id' => 'nullable|integer'
    ]);

    DB::beginTransaction();
    try {
        $inputIds = [];
        foreach ($validated['credentials'] as $cred) {
            if (!empty($cred['h_credentials_id'])) {
                // 既存 → 更新
                HCredential::where('h_credentials_id', $cred['h_credentials_id'])->update([
                    'license_id' => $cred['license_id'],
                    'acquisition_date' => $cred['acquisition_date'],
                    'expiration_date' => $cred['expiration_date'],
                    'valid_flg' => $cred['valid_flg'],
                    'del_flg' => 0
                ]);
                $inputIds[] = $cred['h_credentials_id'];
            } else {
                // 新規作成
                $new = HCredential::create([
                    'member_id' => $id,
                    'license_id' => $cred['license_id'],
                    'acquisition_date' => $cred['acquisition_date'],
                    'expiration_date' => $cred['expiration_date'],
                    'valid_flg' => $cred['valid_flg'],
                    'del_flg' => 0,
                ]);
                $inputIds[] = $new->h_credentials_id;
            }
        }

        // 入力に含まれなかった既存レコードを削除（del_flg = 1）
        HCredential::where('member_id', $id)
            ->whereNotIn('h_credentials_id', $inputIds)
            ->update(['del_flg' => 1]);

        DB::commit();
        return response()->json(['message' => '保有資格情報を更新しました']);
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('保有資格更新エラー', ['error' => $e->getMessage()]);
        return response()->json(['message' => '更新に失敗しました'], 500);
    }
    }

    public function getForMember($id)
    {
        return HCredential::with('license')
            ->where('member_id', $id)
            ->where('del_flg', 0)
            ->get();
    }

}
