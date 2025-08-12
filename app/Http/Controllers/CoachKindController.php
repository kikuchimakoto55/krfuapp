<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCoachKindRequest;
use App\Http\Requests\UpdateCoachKindRequest;
use App\Models\CoachKind;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CoachKindController extends Controller
{
    public function index(Request $request)
    {
        $items = CoachKind::where('del_flg', 0)
            ->orderBy('c_categorykinds_id', 'asc')
            ->get();

        return response()->json($items, 200);
    }

    public function show(int $id)
    {
        $k = CoachKind::where('c_categorykinds_id', $id)
            ->where('del_flg', 0)
            ->first();

        if (!$k) {
            return response()->json(['message' => 'Not Found'], 404);
        }
        return response()->json($k, 200);
    }

    public function store(StoreCoachKindRequest $request)
    {
        Log::info('CoachKind store start', [
            'payload' => $request->all(),
            'user_id' => optional($request->user())->member_id,
        ]);

        $data = $request->validated();
        $data['registration_date'] = now();
        $data['updated_at']        = now();
        $data['del_flg']           = 0;

        $kind = CoachKind::create($data);

        Log::info('CoachKind store success', ['id' => $kind->getKey()]);
        return response()->json(['message' => '登録完了', 'data' => $kind], 201);
    }

    public function update(UpdateCoachKindRequest $request, int $id)
    {
        $k = CoachKind::where('c_categorykinds_id', $id)
            ->where('del_flg', 0)
            ->first();

        if (!$k) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        $data = $request->validated();
        $data['updated_at'] = now();

        $k->fill($data)->save();

        return response()->json(['message' => '更新完了', 'data' => $k], 200);
    }

    public function destroy(int $id)
    {
        $k = CoachKind::where('c_categorykinds_id', $id)
            ->where('del_flg', 0)
            ->first();

        if (!$k) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        $k->del_flg   = 1;      // 論理削除
        $k->updated_at = now();
        $k->save();

        return response()->json(['message' => '削除完了'], 200);
    }
}