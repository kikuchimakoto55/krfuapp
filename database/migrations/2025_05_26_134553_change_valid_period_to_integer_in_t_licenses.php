<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('t_licenses', function (Blueprint $table) {
    $table->integer('valid_period')->change(); // `date` → `integer`
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
{
    Schema::table('t_licenses', function (Blueprint $table) {
        $table->date('valid_period')->change(); // 元の型に戻す
    });
}
};
