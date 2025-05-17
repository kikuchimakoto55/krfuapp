<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Venue;

class VenueController extends Controller
{
    public function index()
    {
        $venues = Venue::where('del_flg', 0)->get(); // 削除フラグが立っていないものだけ取得
        return response()->json($venues);
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