<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. AUTO_INCREMENT を削除（id から）
        DB::statement('ALTER TABLE t_events MODIFY id BIGINT UNSIGNED NOT NULL');

        // 2. 主キー削除
        DB::statement('ALTER TABLE t_events DROP PRIMARY KEY');

        // 3. id カラム削除
        DB::statement('ALTER TABLE t_events DROP COLUMN id');

        // 4. event_id を AUTO_INCREMENT & 主キーに変更
        DB::statement('ALTER TABLE t_events MODIFY event_id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY');

        // 5. 日付カラムのリネーム（LaravelでOK）
        Schema::table('t_events', function (Blueprint $table) {
            $table->renameColumn('registration_date', 'created_at');
            $table->renameColumn('update_date', 'updated_at');
        });
    }

    public function down(): void
    {
        // 1. 日付カラム名を戻す
        Schema::table('t_events', function (Blueprint $table) {
            $table->renameColumn('created_at', 'registration_date');
            $table->renameColumn('updated_at', 'update_date');
        });

        // 2. 主キー削除 & event_id から AUTO_INCREMENT を除去
        DB::statement('ALTER TABLE t_events DROP PRIMARY KEY');
        DB::statement('ALTER TABLE t_events MODIFY event_id INT UNSIGNED NOT NULL');

        // 3. id カラムを復元
        DB::statement('ALTER TABLE t_events ADD COLUMN id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST');
    }
};