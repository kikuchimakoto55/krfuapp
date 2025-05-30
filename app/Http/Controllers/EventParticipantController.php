<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EventParticipantController extends Controller
{
    // イベントにファイルをアップロード
    public function store(Request $request, $event_id)
    {
        //
    }

    // イベントに紐づくファイル一覧を取得
    public function index($event_id)
    {
        //
    }

    // 特定のファイルを削除
    public function destroy($id)
    {
        //
    }
}
