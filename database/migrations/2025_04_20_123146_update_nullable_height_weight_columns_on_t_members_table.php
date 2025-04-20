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
    Schema::table('t_members', function (Blueprint $table) {
        $table->smallInteger('height')->nullable()->change();
        $table->smallInteger('weight')->nullable()->change();
    });
}

public function down()
{
    Schema::table('t_members', function (Blueprint $table) {
        $table->smallInteger('height')->nullable(false)->change();
        $table->smallInteger('weight')->nullable(false)->change();
    });
}

};
