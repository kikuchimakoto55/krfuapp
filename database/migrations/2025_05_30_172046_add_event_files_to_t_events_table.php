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
    Schema::table('t_events', function (Blueprint $table) {
        $table->text('event_files')->nullable()->after('event_overview'); // カンマ区切り or JSON形式
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('t_events', function (Blueprint $table) {
            //
        });
    }
};
