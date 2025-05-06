<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveScoreSummaryColumnsFromTGamesTable extends Migration
{
    public function up()
    {
        Schema::table('t_games', function (Blueprint $table) {
            $table->dropColumn([
                'team1_score1st_point',
                'team1_score2nd_point',
                'team2_score1st_point',
                'team2_score2nd_point',
            ]);
        });
    }

    public function down()
    {
        Schema::table('t_games', function (Blueprint $table) {
            $table->smallInteger('team1_score1st_point')->nullable();
            $table->smallInteger('team1_score2nd_point')->nullable();
            $table->smallInteger('team2_score1st_point')->nullable();
            $table->smallInteger('team2_score2nd_point')->nullable();
        });
    }
}
