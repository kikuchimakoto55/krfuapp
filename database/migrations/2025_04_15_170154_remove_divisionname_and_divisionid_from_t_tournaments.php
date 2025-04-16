<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('t_tournaments', function (Blueprint $table) {
            $table->dropColumn('divisionname');
            $table->dropColumn('divisionid');
        });
    }

    public function down(): void
    {
        Schema::table('t_tournaments', function (Blueprint $table) {
            $table->string('divisionname', 100)->nullable();
            $table->integer('divisionid')->nullable();
        });
    }
};
