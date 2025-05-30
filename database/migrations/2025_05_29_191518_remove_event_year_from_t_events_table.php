<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('t_events', function (Blueprint $table) {
        $table->dropColumn('event_year');
    });
}

public function down(): void
{
    Schema::table('t_events', function (Blueprint $table) {
        $table->date('event_year')->nullable();
    });
}
};
