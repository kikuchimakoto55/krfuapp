<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::table('t_events', function (Blueprint $table) {
        $table->smallInteger('del_flg')->default(0)->change();
    });
}

public function down(): void
{
    Schema::table('t_events', function (Blueprint $table) {
        $table->smallInteger('del_flg')->change(); // 元に戻す（デフォルト値なし）
    });
}
};
