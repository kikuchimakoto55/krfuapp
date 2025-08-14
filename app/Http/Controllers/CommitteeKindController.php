<?php

namespace App\Http\Controllers;

use App\Models\CommitteeKind;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Http\Requests\StoreCommitteeKindRequest;
use App\Http\Requests\UpdateCommitteeKindRequest;

class CommitteeKindController extends Controller
{
    // 一覧（有効のみ）
    public function index()
    {
        $items = CommitteeKind::where('del_flg', 0)
            ->orderBy('committeekinds_id')
            ->get();

        return response()->json($items, 200);
    }

    // 詳細（有効のみ）
    public function show(int $id)
    {
        $item = CommitteeKind::where('committeekinds_id', $id)
            ->where('del_flg', 0)
            ->first();

        if (!$item) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        return response()->json($item, 200);
    }

    // 登録
    public function store(StoreCommitteeKindRequest $request)
    {
        Log::info('CommitteeKind store start', ['name' => $request->committeekindsname]);

        $now = Carbon::now();
        $created = CommitteeKind::create([
            'committeekindsname' => $request->committeekindsname,
            'registration_date'  => $now,
            'updated_at'         => $now,
            'del_flg'            => 0,
        ]);

        Log::info('CommitteeKind store success', ['id' => $created->committeekinds_id]);

        return response()->json($created, 201);
    }

    // 更新（有効行のみ更新可）
    public function update(UpdateCommitteeKindRequest $request, int $id)
    {
        $item = CommitteeKind::where('committeekinds_id', $id)
            ->where('del_flg', 0)
            ->first();

        if (!$item) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        $item->fill([
            'committeekindsname' => $request->committeekindsname,
            'updated_at'         => Carbon::now(),
        ])->save();

        return response()->json($item, 200);
    }

    // 論理削除
    public function destroy(int $id)
    {
        $item = CommitteeKind::where('committeekinds_id', $id)
            ->where('del_flg', 0)
            ->first();

        if (!$item) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        $item->del_flg    = 1;
        $item->updated_at = Carbon::now();
        $item->save();

        return response()->json(['message' => 'deleted'], 200);
    }
}