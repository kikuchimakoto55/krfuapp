<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTTournamentResultsTable extends Migration
{
    public function up()
    {
        Schema::create('t_tournament_results', function (Blueprint $table) {
            $table->bigIncrements('result_id'); // 結果ID（PK）
            $table->unsignedBigInteger('tournament_id'); // 大会ID（FK）
            $table->integer('division_order'); // ディビジョン並び順
            $table->string('division_name'); // ディビジョン名
            $table->string('rank_label'); // 順位名（例：優勝）
            $table->unsignedBigInteger('team_id'); // チームID（FK）
            $table->string('document_path')->nullable(); // 対戦票のパス
            $table->text('report')->nullable(); // 結果レポート

            $table->timestamps();

            // 外部キー制約
            $table->foreign('tournament_id')->references('tournament_id')->on('t_tournaments')->onDelete('cascade');
            $table->foreign('team_id')->references('id')->on('t_teams')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('t_tournament_results');
    }
}
