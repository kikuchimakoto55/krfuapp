<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeTeamIdNullableInTTournamentResultsTable extends Migration
{
    public function up()
    {
        Schema::table('t_tournament_results', function (Blueprint $table) {
            $table->unsignedBigInteger('team_id')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('t_tournament_results', function (Blueprint $table) {
            $table->unsignedBigInteger('team_id')->nullable(false)->change();
        });
    }
}
