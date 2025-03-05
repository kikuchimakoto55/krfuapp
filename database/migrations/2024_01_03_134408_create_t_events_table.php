<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('t_events', function (Blueprint $table) {
            $table->bigIncrements("id")->comment('システムID')->nullable(false);
            $table->integer("event_id")->comment('イベントID')->nullable(false)->unique();
            $table->string('event_name',100)->comment('イベント名')->nullable(false);
            $table->date('event_date')->comment('イベント開催日')->nullable(false);
            $table->datetime('event_opentime', $precision = 0)->comment('イベント開催時間')->nullable(true);
            $table->smallInteger('weather')->comment('当日天気')->nullable(true);
            $table->smallInteger('temperature')->comment('当日気温')->nullable(true);
            $table->integer('venue_id')->comment('会場ID')->nullable(false);
            $table->date('event_year')->comment('イベント開催年度')->nullable(false);
            $table->smallInteger('event_kinds')->comment('イベント種別')->nullable(true);
            $table->text('event_overview')->comment('イベント概要')->nullable(true);
            $table->datetime('registration_date', $precision = 0)->comment('登録日')->nullable(false);
            $table->datetime('update_date', $precision = 0)->comment('更新日')->nullable(false);
            $table->smallInteger('del_flg')->comment('削除フラグ')->nullable(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_event');
    }
};
