<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAPositionKindRequest;
use App\Http\Requests\UpdateAPositionKindRequest;
use App\Models\APositionKind;
use Illuminate\Http\JsonResponse;

class APositionKindController extends Controller
{
    /**
     * 一覧
     */
    public function index(): JsonResponse
    {
        $items = APositionKind::orderBy('a_positionkinds_id', 'asc')->get();
        return response()->json($items);
    }

    /**
     * 詳細
     */
    public function show(int $id): JsonResponse
    {
        $item = APositionKind::findOrFail($id);
        return response()->json($item);
    }

    /**
     * 登録
     */
    public function store(StoreAPositionKindRequest $request): JsonResponse
    {
        $now = now();
        $item = APositionKind::create([
            'a_positionkindskindsname' => $request->input('a_positionkindskindsname'),
            'registration_date' => $now,
            'update_date' => $now,
            'del_flg' => 0,
        ]);

        return response()->json($item, 201);
    }

    /**
     * 更新
     */
    public function update(UpdateAPositionKindRequest $request, int $id): JsonResponse
    {
        $item = APositionKind::findOrFail($id);
        $item->a_positionkindskindsname = $request->input('a_positionkindskindsname');
        $item->update_date = now();
        $item->save();

        return response()->json($item);
    }

    /**
     * 論理削除
     */
    public function destroy(int $id): JsonResponse
    {
        $item = APositionKind::withoutGlobalScope('not_deleted')->findOrFail($id);
        $item->del_flg = 1;
        $item->update_date = now();
        $item->save();

        return response()->json([
            'message' => '削除が完了しました。'
        ]);
    }
}
