<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
}
