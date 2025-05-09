<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('t_scores', function (Blueprint $table) {
        // チーム1後半
        $table->smallInteger('op1hh_t')->nullable();
        $table->smallInteger('op1hh_g')->nullable();
        $table->smallInteger('op1hh_pg')->nullable();
        $table->smallInteger('op1hh_dg')->nullable();
        $table->smallInteger('op1hh_score')->nullable();
        $table->smallInteger('op1hh_pkscore')->nullable();
        $table->smallInteger('op1hh_fkscore')->nullable();

        // チーム2後半
        $table->smallInteger('op2hh_t')->nullable();
        $table->smallInteger('op2hh_g')->nullable();
        $table->smallInteger('op2hh_pg')->nullable();
        $table->smallInteger('op2hh_dg')->nullable();
        $table->smallInteger('op2hh_score')->nullable();
        $table->smallInteger('op2hh_pkscore')->nullable();
        $table->smallInteger('op2hh_fkscore')->nullable();
    });
}

public function down()
{
    Schema::table('t_scores', function (Blueprint $table) {
        $table->dropColumn([
            'op1hh_t', 'op1hh_g', 'op1hh_pg', 'op1hh_dg', 'op1hh_score', 'op1hh_pkscore', 'op1hh_fkscore',
            'op2hh_t', 'op2hh_g', 'op2hh_pg', 'op2hh_dg', 'op2hh_score', 'op2hh_pkscore', 'op2hh_fkscore'
        ]);
    });
}

};
