<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1) その列に付いている外部キー名を動的に取得して DROP
        $fk = DB::selectOne("
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 't_coaches'
              AND COLUMN_NAME = 'c_categorykinds_id'
              AND REFERENCED_TABLE_NAME IS NOT NULL
            LIMIT 1
        ");
        if ($fk && isset($fk->CONSTRAINT_NAME)) {
            DB::statement("ALTER TABLE t_coaches DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
        }

        // 2) 型を unsigned のまま nullable に変更
        Schema::table('t_coaches', function (Blueprint $table) {
            $table->unsignedInteger('c_categorykinds_id')->nullable()->change();
            $table->string('c_categorykindsname', 255)->nullable()->change();
        });

        // 3) 外部キーを再作成（SET NULL / CASCADE）
        Schema::table('t_coaches', function (Blueprint $table) {
            $table->foreign('c_categorykinds_id', 'fk_coaches_kind')
                ->references('c_categorykinds_id')->on('t_coach_kinds')
                ->onUpdate('cascade')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        // 1) 付け直した FK を安全に DROP（存在すれば）
        $fk = DB::selectOne("
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 't_coaches'
              AND COLUMN_NAME = 'c_categorykinds_id'
              AND REFERENCED_TABLE_NAME IS NOT NULL
            LIMIT 1
        ");
        if ($fk && isset($fk->CONSTRAINT_NAME)) {
            DB::statement("ALTER TABLE t_coaches DROP FOREIGN KEY `{$fk->CONSTRAINT_NAME}`");
        }

        // 2) 非NULLへ戻す（unsigned のまま）
        Schema::table('t_coaches', function (Blueprint $table) {
            $table->unsignedInteger('c_categorykinds_id')->nullable(false)->change();
            $table->string('c_categorykindsname', 255)->nullable(false)->change();
        });

        // 3) 元の制約を戻す（必要なら restrict へ）
        Schema::table('t_coaches', function (Blueprint $table) {
            $table->foreign('c_categorykinds_id', 'fk_coaches_kind')
                ->references('c_categorykinds_id')->on('t_coach_kinds')
                ->onUpdate('cascade')
                ->onDelete('restrict');
        });
    }
};