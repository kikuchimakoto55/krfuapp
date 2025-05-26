<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('t_licenses', function (Blueprint $table) {
            // 旧カラムを削除
            $table->dropColumn(['registration_date', 'update_date']);

            // 新しい created_at / updated_at を追加
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::table('t_licenses', function (Blueprint $table) {
            // 元に戻す
            $table->dateTime('registration_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->dateTime('update_date')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'));

            $table->dropTimestamps();
        });
    }
};