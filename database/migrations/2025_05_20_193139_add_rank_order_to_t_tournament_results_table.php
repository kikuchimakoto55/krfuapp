<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRankOrderToTTournamentResultsTable extends Migration
{
    public function up()
    {
        Schema::table('t_tournament_results', function (Blueprint $table) {
            $table->integer('rank_order')->after('division_name'); // 順序は適宜調整
        });
    }

    public function down()
    {
        Schema::table('t_tournament_results', function (Blueprint $table) {
            $table->dropColumn('rank_order');
        });
    }
};
