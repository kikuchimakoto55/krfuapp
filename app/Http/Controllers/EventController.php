<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use App\Http\Requests\StoreEventRequest;
use App\Http\Requests\UpdateEventRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class EventController extends Controller
{
    // 一覧取得
    public function index()
{
    $events = Event::where('del_flg', 0)
        ->orderBy('event_opentime', 'desc')
        ->get([
            'event_id',
            'event_name',
            'event_opentime',
            'venue_name',
            'event_kinds',
            'event_overview',
            'weather',
            'temperature',
            'event_files',
        ]);

    return response()->json($events);
}

    // 新規登録
    public function store(StoreEventRequest $request)
    {
        $event = Event::create($request->validated());
        return response()->json($event, 201);
    }

    // 詳細取得
    public function show($id)
    {
        $event = Event::findOrFail($id);
        return response()->json($event);
    }

    // 更新
    public function update(UpdateEventRequest $request, $id)
{
    $event = Event::findOrFail($id);

    // 基本情報更新
    $event->fill($request->validated());

    if ($request->hasFile('event_files')) {
        $uploadedFiles = $request->file('event_files');
        $savedFiles = [];

        $timestamp = now()->format('Ymd_His');
        $dir = "public/events/{$timestamp}";

        foreach ($uploadedFiles as $file) {
            $originalName = $file->getClientOriginalName();
            $filename = pathinfo($originalName, PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $uniqueName = $filename . '_' . Str::random(8) . '.' . $extension;

            $path = $file->storeAs($dir, $uniqueName);
            $savedFiles[] = str_replace('public/', '', $path);
        }

        $event->event_files = implode(',', $savedFiles);
    }

    $event->save();

    return response()->json($event);
}

    // 論理削除
    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        $event->del_flg = 1;
        $event->save();

        return response()->json(['message' => '削除しました'], 200);
    }
}
