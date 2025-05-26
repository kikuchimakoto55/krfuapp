<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('t_licenses', function (Blueprint $table) {
            $table->renameColumn('year', 'valid_period'); // 有効期限 → 資格保有期間
            $table->dropColumn('expiredate');             // 失効日を削除（t_h_credentials で管理）
        });
    }

    public function down(): void
    {
        Schema::table('t_licenses', function (Blueprint $table) {
            $table->renameColumn('valid_period', 'year');
            $table->dateTime('expiredate');
        });
    }
};