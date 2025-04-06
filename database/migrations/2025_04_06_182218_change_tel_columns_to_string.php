<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('t_members', function (Blueprint $table) {
            $table->string('guardian_tel', 11)->change();
            $table->string('tel', 11)->nullable()->change(); // NULL許可されている場合
            $table->string('emergency_tel1', 11)->change();
        });
    }

    public function down()
    {
        Schema::table('t_members', function (Blueprint $table) {
            $table->unsignedBigInteger('guardian_tel')->change();
            $table->unsignedBigInteger('tel')->nullable()->change();
            $table->unsignedBigInteger('emergency_tel1')->change();
        });
    }
};

