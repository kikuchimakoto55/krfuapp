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
            $table->dateTime('login_date')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('t_members', function (Blueprint $table) {
            $table->dateTime('login_date')->nullable(false)->change();
        });
    }
};
