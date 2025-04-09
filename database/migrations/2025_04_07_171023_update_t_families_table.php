<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('t_families', function (Blueprint $table) {
            // 不要なカラムを削除
            $table->dropColumn(['familymaster_id', 'fname']);
        });

        // timestamp型の変更（別ブロックで）
        DB::statement("ALTER TABLE t_families MODIFY registration_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP");
        DB::statement("ALTER TABLE t_families MODIFY update_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
    }

    public function down(): void
    {
        Schema::table('t_families', function (Blueprint $table) {
            $table->integer('familymaster_id')->nullable(false)->unique()->comment('家族管理ID');
            $table->string('fname', 50)->nullable(false)->comment('家族氏名');

            $table->dateTime('registration_date')->nullable(false)->comment('登録日')->change();
            $table->dateTime('update_date')->nullable(false)->comment('更新日')->change();
        });
    }
};
