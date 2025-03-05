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
        Schema::create('t_scores', function (Blueprint $table) {
            $table->bigIncrements("id")->comment('システムID')->nullable(false);
            $table->integer("score_id")->comment('スコア管理ID')->nullable(false)->unique();
            $table->integer('game_id')->comment('試合管理ID')->nullable(false);
            $table->smallInteger('op1fh_t')->comment('チーム１前半Ｔ')->nullable(true);
            $table->smallInteger('op1fh_g')->comment('チーム１前半Ｇ')->nullable(true);
            $table->smallInteger('op1fh_pg')->comment('チーム１前半PG')->nullable(true);
            $table->smallInteger('op1fh_dg')->comment('チーム１前半DG')->nullable(true);
            $table->smallInteger('op1fh_score')->comment('チーム１前半得点')->nullable(true);
            $table->smallInteger('op1fh_pkscore')->comment('チーム１前半PK')->nullable(true);
            $table->smallInteger('op1fh_fkscore')->comment('チーム１前半FK')->nullable(true);
            $table->smallInteger('op1fh_result')->comment('チーム１勝敗')->nullable(true);
            $table->smallInteger('op2fh_t')->comment('チーム２前半Ｔ')->nullable(true);
            $table->smallInteger('op2fh_g')->comment('チーム２前半Ｇ')->nullable(true);
            $table->smallInteger('op2fh_pg')->comment('チーム２前半PG')->nullable(true);
            $table->smallInteger('op2fh_dg')->comment('チーム２前半DG')->nullable(true);
            $table->smallInteger('op2fh_score')->comment('チーム２前半得点')->nullable(true);
            $table->smallInteger('op2fh_pkscore')->comment('チーム２前半PK')->nullable(true);
            $table->smallInteger('op2fh_pkfkscore')->comment('チーム２前半PKFK合計')->nullable(true);
            $table->smallInteger('op2fh_result')->comment('チーム２勝敗')->nullable(true);
            $table->string('score_book')->comment('スコアブック')->nullable(true);
            $table->text('gamereport')->comment('ゲームレポート')->nullable(true);
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
        Schema::dropIfExists('t_score');
    }
};
