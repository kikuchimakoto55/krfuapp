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
        Schema::create('t_tournaments', function (Blueprint $table) {
            $table->bigIncrements("id")->comment('システムID')->nullable(false);
            $table->integer("tournament_id")->comment('大会管理ID')->nullable(false)->unique();
            $table->smallInteger('categoly')->comment('カテゴリ')->nullable(false);
            $table->date('year')->comment('年度')->nullable(false);
            $table->datetime('event_period_start', $precision = 0)->comment('開催期間スタート')->nullable(false);
            $table->datetime('event_period_end', $precision = 0)->comment('開催期間エンド')->nullable(true);
            $table->string('name',100)->comment('大会名')->nullable(false);
            $table->smallInteger('divisionflg')->comment('ディビジョン可否')->nullable(false);
            $table->string('divisionname',100)->comment('ディビジョン名')->nullable(true);
            $table->integer('divisionid')->comment('ディビジョンID')->nullable(true);
            $table->smallInteger('publishing')->comment('公開設定')->nullable(false);
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
        Schema::dropIfExists('t_tournament');
    }
};
