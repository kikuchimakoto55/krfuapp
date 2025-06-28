<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Member;
use Illuminate\Support\Facades\Log;

class InitialPasswordController extends Controller
{
    public function change(Request $request, $id)
    {
    $request->validate([
        'password' => 'required|confirmed|min:8',
    ]);

    $member = Member::findOrFail($id);

    // すでに初回変更済みチェック
    if (!is_null($member->login_date)) {
        return response()->json(['message' => 'すでに初回変更済みです'], 403);
    }

    $member->password = Hash::make($request->password);
    $member->login_date = now();
    $member->save();

    return response()->json(['message' => 'パスワードを変更しました']);
    }

}
