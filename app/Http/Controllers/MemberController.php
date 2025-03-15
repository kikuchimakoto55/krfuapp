<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;

class MemberController extends Controller
{
    public function index()
    {
        // 全会員情報を取得（削除済み会員は除外）
        $members = Member::where('del_flg', 0)->get();

        return response()->json($members);
    }
}
