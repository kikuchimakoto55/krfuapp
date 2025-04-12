<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FamilyController extends Controller

{
    public function store(Request $request)
    {
        // バリデーション（必要に応じて FormRequest に分離もOK）
        $validated = $request->validate([
            'member_id' => 'required|integer|exists:t_members,member_id',
            'family_id' => 'required|integer|exists:t_members,member_id',
            'relationship' => 'required|integer|min:1',
        ]);
    
        // 家族情報を登録（論理削除フラグや日時も）
        $family = \App\Models\Family::create([
            'member_id' => $validated['member_id'],
            'family_id' => $validated['family_id'],
            'relationship' => $validated['relationship'],
            'registration_date' => now(),
            'update_date' => now(),
            'del_flg' => 0,
        ]);
    
        return response()->json(['message' => '家族を登録しました', 'family' => $family], 201);
    }
    //家族編集処理
    public function update(Request $request, $id)
    {
        $request->validate([
            'relationship' => 'required|integer|min:1|max:9',
        ]);

        $updated = DB::table('t_families')
            ->where('id', $id)
            ->update([
                'relationship' => $request->input('relationship'),
            ]);

        if ($updated) {
            return response()->json(['message' => '続柄を更新しました']);
        } else {
            return response()->json(['message' => '更新対象が見つかりません'], 404);
        }
    }
    //家族解除処理
    public function destroy($id)
    {
    $family = \DB::table('t_families')->where('id', $id)->first();

    if (!$family) {
        return response()->json(['message' => '家族情報が見つかりません'], 404);
    }

    \DB::table('t_families')->where('id', $id)->delete();

    return response()->json(['message' => '家族情報を解除しました']);
    }

    //家族検索処理
    public function search(Request $request)
    {
    $memberId = $request->input('member_id');

    if (!$memberId) {
        return response()->json([], 400);
    }

    $families = DB::table('t_families')
        ->join('t_members', 't_families.family_id', '=', 't_members.member_id')
        ->where('t_families.member_id', $memberId)
        ->select('t_members.*', 't_families.relationship')
        ->get();

    return response()->json($families);
    }

}
