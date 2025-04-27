<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTGamesTable extends Migration
{
    public function up()
    {
        Schema::create('t_games', function (Blueprint $table) {
            $table->bigIncrements('game_id'); // 試合ID（PK）
            $table->unsignedBigInteger('tournament_id'); // 大会ID（FK）
            $table->integer('division_order'); // ディビジョン並び順
            $table->string('round_label')->nullable(); // 回戦（例: 1回戦, 準決勝）
            $table->dateTime('game_date')->nullable(); // 開催日時
            $table->unsignedBigInteger('venue_id')->nullable(); // 会場ID（別テーブル参照）
            $table->unsignedBigInteger('team1_id')->nullable(); // チーム1（対戦チーム1）
            $table->unsignedBigInteger('team2_id')->nullable(); // チーム2（対戦チーム2）
            $table->string('referee')->nullable(); // レフリー
            $table->string('person_in_charge')->nullable(); // 担当者
            $table->string('doctor')->nullable(); // ドクター
            $table->timestamps();

            // 外部キー制約（必要に応じて）
            $table->foreign('tournament_id')->references('tournament_id')->on('t_tournaments')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('t_games');
    }
}
