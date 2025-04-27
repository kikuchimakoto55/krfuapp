<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDivisionNameToTGamesTable extends Migration
{
    public function up()
    {
        Schema::table('t_games', function (Blueprint $table) {
            $table->string('division_name')->nullable()->after('tournament_id');
        });
    }

    public function down()
    {
        Schema::table('t_games', function (Blueprint $table) {
            $table->dropColumn('division_name');
        });
    }
}
