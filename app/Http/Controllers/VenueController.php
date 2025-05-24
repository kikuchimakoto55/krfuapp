<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venue;

class VenueController extends Controller
{
    public function index(Request $request)
{

    $query = Venue::where('del_flg', 0);

    // 会場名で部分一致（空文字も許可）
    if (isset($request->venue_name)) {
        $query->where('venue_name', 'like', '%' . $request->venue_name . '%');
    }

    // 住所で部分一致
    if (isset($request->address)) {
        $query->where('address', 'like', '%' . $request->address . '%');
    }

    // 駐車場（0 or 1）明示チェック
    if ($request->has('parking') && $request->parking !== '') {
        $query->where('parking', $request->parking);
    }

    return response()->json($query->get());
}

    public function store(Request $request)
    {
    $validated = $request->validate([
        'venue_name' => 'required|string|max:255',
        'zip' => 'required|string|max:10',
        'address' => 'required|string|max:255',
        'tel' => 'nullable|string|max:20',
        'mapurl' => 'nullable|string|max:255',
        'hpurl' => 'nullable|string|max:255',
        'parking' => 'nullable|integer',
        'parking_number' => 'nullable|string|max:50',
        'remarks' => 'nullable|string',
    ]);

    $venue = Venue::create($validated);

    return response()->json(['message' => '会場情報を登録しました', 'venue' => $venue], 201);
    }
    public function show($id)
    {
        $venue = Venue::findOrFail($id);
        return response()->json($venue);
    }

    public function update(Request $request, $id)
    {
        $venue = Venue::findOrFail($id);

        $validated = $request->validate([
            'venue_name' => 'required|string|max:255',
            'zip' => 'required|string|max:10',
            'address' => 'required|string|max:255',
            'tel' => 'nullable|string|max:20',
            'mapurl' => 'nullable|string|max:255',
            'hpurl' => 'nullable|string|max:255',
            'parking' => 'nullable|integer',
            'parking_number' => 'nullable|string|max:50',
            'remarks' => 'nullable|string',
        ]);

        $venue->update($validated);

        return response()->json(['message' => '会場情報を更新しました', 'venue' => $venue]);
    }

    // 会場削除
    public function destroy($id)
    {
        $venue = \App\Models\Venue::findOrFail($id);
        $venue->update(['del_flg' => 1]);
        return response()->json(['message' => '会場を論理削除しました']);
    }
}