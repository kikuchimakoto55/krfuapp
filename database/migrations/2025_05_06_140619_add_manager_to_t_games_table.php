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
    Schema::table('t_games', function (Blueprint $table) {
        $table->string('manager')->nullable()->after('doctor'); // doctor の後に追加（順番はお好みで）
    });
}

public function down()
{
    Schema::table('t_games', function (Blueprint $table) {
        $table->dropColumn('manager');
    });
}
};
