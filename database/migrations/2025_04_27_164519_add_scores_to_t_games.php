<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddScoresToTGames extends Migration
{
    public function up()
    {
        Schema::table('t_games', function (Blueprint $table) {
            // チームA 前半・後半
            $table->smallInteger('team1_score1st_point')->nullable()->after('approval_flg');
            $table->smallInteger('team1_score1st_try')->nullable();
            $table->smallInteger('team1_score1st_goal')->nullable();
            $table->smallInteger('team1_score1st_pg')->nullable();
            $table->smallInteger('team1_score1st_dg')->nullable();
            $table->smallInteger('team1_score2nd_point')->nullable();
            $table->smallInteger('team1_score2nd_try')->nullable();
            $table->smallInteger('team1_score2nd_goal')->nullable();
            $table->smallInteger('team1_score2nd_pg')->nullable();
            $table->smallInteger('team1_score2nd_dg')->nullable();

            // チームB 前半・後半
            $table->smallInteger('team2_score1st_point')->nullable();
            $table->smallInteger('team2_score1st_try')->nullable();
            $table->smallInteger('team2_score1st_goal')->nullable();
            $table->smallInteger('team2_score1st_pg')->nullable();
            $table->smallInteger('team2_score1st_dg')->nullable();
            $table->smallInteger('team2_score2nd_point')->nullable();
            $table->smallInteger('team2_score2nd_try')->nullable();
            $table->smallInteger('team2_score2nd_goal')->nullable();
            $table->smallInteger('team2_score2nd_pg')->nullable();
            $table->smallInteger('team2_score2nd_dg')->nullable();
        });
    }

    public function down()
    {
        Schema::table('t_games', function (Blueprint $table) {
            $table->dropColumn([
                'team1_score1st_point', 'team1_score1st_try', 'team1_score1st_goal', 'team1_score1st_pg', 'team1_score1st_dg',
                'team1_score2nd_point', 'team1_score2nd_try', 'team1_score2nd_goal', 'team1_score2nd_pg', 'team1_score2nd_dg',
                'team2_score1st_point', 'team2_score1st_try', 'team2_score1st_goal', 'team2_score1st_pg', 'team2_score1st_dg',
                'team2_score2nd_point', 'team2_score2nd_try', 'team2_score2nd_goal', 'team2_score2nd_pg', 'team2_score2nd_dg'
            ]);
        });
    }
}
