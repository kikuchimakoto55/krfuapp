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
    Schema::table('t_scores', function (Blueprint $table) {
        $table->smallInteger('op1_total_score')->nullable()->comment('チーム1総得点');
        $table->smallInteger('op2_total_score')->nullable()->comment('チーム2総得点');
    });
}

public function down()
{
    Schema::table('t_scores', function (Blueprint $table) {
        $table->dropColumn(['op1_total_score', 'op2_total_score']);
    });
}
};
