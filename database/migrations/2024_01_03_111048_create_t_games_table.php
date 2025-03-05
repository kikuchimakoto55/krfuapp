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
        Schema::create('t_games', function (Blueprint $table) {
            $table->bigIncrements("id")->comment('システムID')->nullable(false);
            $table->integer("game_id")->comment('試合管理ID')->nullable(false)->unique();
            $table->smallInteger('categoly')->comment('カテゴリ')->nullable(false);
            $table->datetime('year', $precision = 0)->comment('年度')->nullable(false);
            $table->string('name',100)->comment('大会名')->nullable(false);
            $table->smallInteger('round_info')->comment('回戦情報')->nullable(false);
            $table->datetime('event_period_day', $precision = 0)->comment('開催日時')->nullable(false);
            $table->string('venue',50)->comment('会場')->nullable(false);
            $table->string('opponent1',50)->comment('対戦チーム1')->nullable(false);
            $table->string('opponent2',50)->comment('対戦チーム2')->nullable(false);
            $table->string('referee',30)->comment('レフリー')->nullable(true);
            $table->string('doctor',30)->comment('ドクター')->nullable(true);
            $table->smallInteger('divisionflg')->comment('ディビジョン可否')->nullable(true);
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
        Schema::dropIfExists('t_game');
    }
};
