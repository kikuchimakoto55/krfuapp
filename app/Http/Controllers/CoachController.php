<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCoachRequest;
use App\Models\Coach;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CoachController extends Controller
{
    /**
     * POST /api/coaches
     * 3系統（指導員/委員会/役職）を一括登録
     * - 1種別 = 1レコード
     * - (member_id, role_type, role_kinds_id, del_flg=0) 重複はスキップ
     * - role_kindsname は各マスタから取得して保存
     */
    public function store(StoreCoachRequest $request): JsonResponse
    {
        $v = $request->validated();

        $memberId  = (int) ($v['member_id'] ?? 0);
        // 既存有効レコードの有無
        $hasActive = Coach::where('member_id', $memberId)->where('del_flg', 0)->exists();

        // overwrite パラメータ（?overwrite=1 または body.overwrite=true を許可）
        $overwrite = (bool) $request->boolean('overwrite');// $request->boolean('overwrite') でもOK

        if ($hasActive && !$overwrite) {
            return response()->json([
                'message' => '既に紐づきが存在します。最新情報で上書きしますか？',
                'exists'  => true,
            ], 409);
        }

        $remarks   = $v['remarks']    ?? null;
        $refereeId = $v['referee_id'] ?? null;

        $coachIds     = array_map('intval', $v['coach_kind_ids']      ?? []);
        $committeeIds = array_map('intval', $v['committee_kind_ids']  ?? []);
        $aposIds      = array_map('intval', $v['a_position_kind_ids'] ?? []);

        // === 名称マップ作成 ===
        // 指導員（t_coach_kinds）
        $nameMapCoach = [];
        if (!empty($coachIds)) {
            $rows = DB::table('t_coach_kinds')
                ->whereIn('c_categorykinds_id', $coachIds)
                ->select(['c_categorykinds_id', 'c_categorykindsname'])
                ->get();
            foreach ($rows as $r) {
                $nameMapCoach[(int) $r->c_categorykinds_id] = $r->c_categorykindsname;
            }
        }

        // 委員会（t_committee_kinds）
        $nameMapCommittee = [];
        if (!empty($committeeIds)) {
            $rows = DB::table('t_committee_kinds')
                ->whereIn('committeekinds_id', $committeeIds)
                ->select(['committeekinds_id', 'committeekindsname'])
                ->get();
            foreach ($rows as $r) {
                $nameMapCommittee[(int) $r->committeekinds_id] = $r->committeekindsname;
            }
        }

        // 役職（t_a_positionkinds）
        // ※ 実テーブルの列名が a_positionkindskindsname（kinds が重複）なのでエイリアスで吸収
        $nameMapAPos = [];
        if (!empty($aposIds)) {
            $rows = DB::table('t_a_positionkinds')
                ->whereIn('a_positionkinds_id', $aposIds)
                ->select([
                    'a_positionkinds_id',
                    DB::raw('a_positionkindskindsname as a_positionkindsname'),
                ])
                ->get();
            foreach ($rows as $r) {
                $nameMapAPos[(int) $r->a_positionkinds_id] = $r->a_positionkindsname;
            }
        }

        $created = [];
        $skipped = [];

        DB::beginTransaction();
        try {
            if ($hasActive && $overwrite) {
            // 既存を全て論理削除（全置換の下ごしらえ）
                Coach::where('member_id', $memberId)
                    ->where('del_flg', 0)
                    ->update([
                        'del_flg'     => 1,
                        'update_date' => now(),
                    ]);
            }
            // === 指導員 ===
            foreach ($coachIds as $id) {
                $exists = Coach::where('member_id', $memberId)
                    ->where('role_type', Coach::ROLE_COACH)
                    ->where('role_kinds_id', $id)
                    ->where('del_flg', 0)
                    ->exists();

                if ($exists) {
                    $skipped[] = [
                        'member_id'     => $memberId,
                        'role_type'     => Coach::ROLE_COACH,
                        'role_kinds_id' => $id,
                    ];
                    continue;
                }

                $rec = Coach::create([
                    'member_id'        => $memberId,
                    'role_type'        => Coach::ROLE_COACH,
                    'role_kinds_id'    => $id,
                    'role_kindsname'   => $nameMapCoach[$id] ?? null,
                    'remarks'          => $remarks,
                    'referee_id'       => $refereeId,
                    'del_flg'          => 0,
                    // 後方互換フィールド（任意保持）
                    'c_categorykinds_id'   => $id,
                    'c_categorykindsname'  => $nameMapCoach[$id] ?? null,
                ]);

                $created[] = [
                    'coach_id'      => $rec->coach_id,
                    'role_type'     => Coach::ROLE_COACH,
                    'role_kinds_id' => $id,
                ];
            }

            // === 委員会 ===
            foreach ($committeeIds as $id) {
                $exists = Coach::where('member_id', $memberId)
                    ->where('role_type', Coach::ROLE_COMMITTEE)
                    ->where('role_kinds_id', $id)
                    ->where('del_flg', 0)
                    ->exists();

                if ($exists) {
                    $skipped[] = [
                        'member_id'     => $memberId,
                        'role_type'     => Coach::ROLE_COMMITTEE,
                        'role_kinds_id' => $id,
                    ];
                    continue;
                }

                $rec = Coach::create([
                    'member_id'      => $memberId,
                    'role_type'      => Coach::ROLE_COMMITTEE,
                    'role_kinds_id'  => $id,
                    'role_kindsname' => $nameMapCommittee[$id] ?? null,
                    'remarks'        => $remarks,
                    'referee_id'     => $refereeId,
                    'del_flg'        => 0,
                ]);

                $created[] = [
                    'coach_id'      => $rec->coach_id,
                    'role_type'     => Coach::ROLE_COMMITTEE,
                    'role_kinds_id' => $id,
                ];
            }

            // === 役職 ===
            foreach ($aposIds as $id) {
                $exists = Coach::where('member_id', $memberId)
                    ->where('role_type', Coach::ROLE_APOSITION)
                    ->where('role_kinds_id', $id)
                    ->where('del_flg', 0)
                    ->exists();

                if ($exists) {
                    $skipped[] = [
                        'member_id'     => $memberId,
                        'role_type'     => Coach::ROLE_APOSITION,
                        'role_kinds_id' => $id,
                    ];
                    continue;
                }

                $rec = Coach::create([
                    'member_id'      => $memberId,
                    'role_type'      => Coach::ROLE_APOSITION,
                    'role_kinds_id'  => $id,
                    'role_kindsname' => $nameMapAPos[$id] ?? null,
                    'remarks'        => $remarks,
                    'referee_id'     => $refereeId,
                    'del_flg'        => 0,
                ]);

                $created[] = [
                    'coach_id'      => $rec->coach_id,
                    'role_type'     => Coach::ROLE_APOSITION,
                    'role_kinds_id' => $id,
                ];
            }

            DB::commit();

            if (empty($created)) {
                return response()->json([
                    'message' => '新規に登録されたデータはありません',
                    'skipped' => $skipped,
                ], 200);
            }

            return response()->json([
                'message' => '登録しました',
                'created' => $created,
                'skipped' => $skipped,
            ], 201);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Coach store error', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => '登録に失敗しました',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(int $coachId): JsonResponse
    {
        $rec = Coach::findOrFail($coachId);

        // 既に論理削除済みなら冪等にOK返す
        if ((int)$rec->del_flg === 1) {
            return response()->json([
                'message'  => '既に削除済みです',
                'coach_id' => $rec->coach_id,
                'del_flg'  => $rec->del_flg,
            ], 200);
        }

        $rec->update([
            'del_flg'     => 1,
            'update_date' => now(),
        ]);

        return response()->json([
            'message'  => '削除しました（論理削除）',
            'coach_id' => $rec->coach_id,
            'del_flg'  => $rec->del_flg,
        ], 200);
    }

    public function update(Request $request, int $coachId): JsonResponse
    {
        $rec = Coach::findOrFail($coachId);

        // バリデーション
        $v = $request->validate([
            'remarks'     => 'nullable|string|max:255',
            'referee_id'  => 'nullable|integer',
            'role_kinds_id' => 'nullable|integer', // 必要なら role_kinds_id 変更を許可
        ]);

        // role_kinds_id を差し替える場合 → 名称再取得
        if (!empty($v['role_kinds_id']) && $v['role_kinds_id'] !== $rec->role_kinds_id) {
            switch ($rec->role_type) {
                case Coach::ROLE_COACH:
                    $name = DB::table('t_coach_kinds')
                        ->where('c_categorykinds_id', $v['role_kinds_id'])
                        ->value('c_categorykindsname');
                    break;
                case Coach::ROLE_COMMITTEE:
                    $name = DB::table('t_committee_kinds')
                        ->where('committeekinds_id', $v['role_kinds_id'])
                        ->value('committeekindsname');
                    break;
                case Coach::ROLE_APOSITION:
                    $name = DB::table('t_a_positionkinds')
                        ->where('a_positionkinds_id', $v['role_kinds_id'])
                        ->value('a_positionkindsname');
                    break;
                default:
                    $name = null;
            }

            if (!$name) {
                return response()->json([
                    'message' => '指定された role_kinds_id が存在しません',
                    'role_kinds_id' => $v['role_kinds_id'],
                ], 422);
            }

            $rec->role_kinds_id   = $v['role_kinds_id'];
            $rec->role_kindsname  = $name;

            // 指導員役職のみ、レガシーカラムも更新
            if ($rec->role_type === Coach::ROLE_COACH) {
                $rec->c_categorykinds_id   = $v['role_kinds_id'];
                $rec->c_categorykindsname  = $name;
            }
        }

        if (array_key_exists('remarks', $v)) {
            $rec->remarks = $v['remarks'];
        }
        if (array_key_exists('referee_id', $v)) {
            $rec->referee_id = $v['referee_id'];
        }

        $rec->update_date = now();
        $rec->save();

        return response()->json([
            'message' => '更新しました',
            'coach_id' => $rec->coach_id,
            'role_type' => $rec->role_type,
            'role_kinds_id' => $rec->role_kinds_id,
            'role_kindsname' => $rec->role_kindsname,
            'remarks' => $rec->remarks,
            'referee_id' => $rec->referee_id,
        ], 200);
    }

    public function index(): JsonResponse
    {
        $items = Coach::with(['member:member_id,username_sei,username_mei']) // ★追加
            ->where('del_flg', 0)
            ->orderBy('member_id')
            ->orderBy('role_type')
            ->orderBy('role_kinds_id')
            ->get([
                'coach_id',
                'member_id',
                'role_type',
                'role_kinds_id',
                'registration_date',
                'update_date',
            ]);
        return response()->json($items);
    }

    public function show(int $coachId): JsonResponse
    {
        $rec = Coach::with(['member:member_id,username_sei,username_mei'])->findOrFail($coachId);
        $memberName = $rec->member
        ? ($rec->member->username_sei . ' ' . $rec->member->username_mei)
        : null;

        return response()->json([
            'coach_id'        => $rec->coach_id,
            'member_id'       => $rec->member_id,
            'role_type'       => $rec->role_type,
            'role_kinds_id'   => $rec->role_kinds_id,
            'role_kindsname'  => $rec->role_kindsname,
            'remarks'         => $rec->remarks,
            'referee_id'      => $rec->referee_id,
            'registration_date' => $rec->registration_date,
            'update_date'       => $rec->update_date,
            'del_flg'         => $rec->del_flg,
            'member_name'        => $memberName,
            'member'             => $rec->member ? [
                    'member_id'     => $rec->member->member_id,
                    'username_sei'  => $rec->member->username_sei,
                    'username_mei'  => $rec->member->username_mei,
                ] : null,
        ]);
    }
}
