<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\License;

class LicenseController extends Controller
{
   public function index(Request $request)
    {
        $query = License::query()->where('del_flg', 0);

        if ($request->has('keyword') && !empty($request->keyword)) {
            $keyword = $request->keyword;
            $query->where(function($q) use ($keyword) {
                $q->where('licensekindsname', 'like', "%{$keyword}%")
                ->orWhere('license_name', 'like', "%{$keyword}%");
            });
        }

        $licenses = $query->orderByDesc('created_at')->get();

        return response()->json($licenses);
    }
    public function store(Request $request)
    {
    $validated = $request->validate([
        'licensekinds_id' => 'required|integer',
        'licensekindsname' => 'required|string|max:50',
        'valid_period' => 'required|integer|min:1',
        'participation_conditions' => 'nullable|string',
        'requirements' => 'nullable|string',
        'requirements_url' => 'nullable|string|max:255',
        'management_organization' => 'nullable|string',
        'del_flg' => 'required|integer',
    ]);

    $license = \App\Models\License::create($validated);

    return response()->json(['message' => '登録完了', 'license' => $license]);
    }

    public function show($id)
    {
    $license = License::findOrFail($id);
    return response()->json($license);
    }

    public function update(Request $request, $id)
    {
    $validated = $request->validate([
        'licensekinds_id' => 'required|integer',
        'licensekindsname' => 'required|string|max:50',
        'valid_period' => 'required|integer|min:1',
        'participation_conditions' => 'nullable|string',
        'requirements' => 'nullable|string',
        'requirements_url' => 'nullable|string|max:255',
        'management_organization' => 'nullable|string',
        'del_flg' => 'required|integer',
    ]);

    $license = License::findOrFail($id);
    $license->update($validated);

    return response()->json(['message' => '更新成功']);
    }

    public function destroy($id)
    {
    $license = License::findOrFail($id);
    $license->del_flg = 1;
    $license->save();

    return response()->json(['message' => '削除しました']);
    }
}
