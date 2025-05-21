<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('t_tournament_results', function (Blueprint $table) {
            $table->tinyInteger('del_flg')->default(0)->after('result_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('t_tournament_results', function (Blueprint $table) {
            $table->dropColumn('del_flg');
        });
    }
};