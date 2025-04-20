<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Team;

class TeamController extends Controller
{
    // 一覧取得
    public function index()
    {
        return response()->json(Team::orderBy('id', 'desc')->get());
    }

    // 登録処理
    public function store(Request $request)
    {
        $team = Team::create($request->all());
        return response()->json($team, 201);
    }

    // 詳細取得
    public function show($id)
    {
        $team = Team::findOrFail($id);
        return response()->json($team);
    }

    // 更新処理
    public function update(Request $request, $id)
    {
        $team = Team::findOrFail($id);
        $team->update($request->all());
        return response()->json($team);
    }

    // 削除処理
    public function destroy($id)
    {
        $team = Team::findOrFail($id);
        $team->delete();
        return response()->json(['message' => '削除しました']);
    }
}
