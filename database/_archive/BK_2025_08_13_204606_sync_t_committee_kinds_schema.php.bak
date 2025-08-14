<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 互換: もし旧列 update_date があって updated_at が無ければリネーム
        if (Schema::hasColumn('t_committee_kinds', 'update_date') && !Schema::hasColumn('t_committee_kinds', 'updated_at')) {
            DB::statement("
                ALTER TABLE t_committee_kinds
                CHANGE `update_date` `updated_at` DATETIME NOT NULL
                DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ");
        }

        // 型・デフォルト・照合順序の統一（既に一致していれば実害なし）
        DB::statement("ALTER TABLE t_committee_kinds MODIFY `committeekindsname` VARCHAR(100) NOT NULL");
        DB::statement("ALTER TABLE t_committee_kinds MODIFY `registration_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP");
        DB::statement("ALTER TABLE t_committee_kinds MODIFY `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
        DB::statement("ALTER TABLE t_committee_kinds MODIFY `del_flg` TINYINT NOT NULL DEFAULT 0");
        DB::statement("ALTER TABLE t_committee_kinds CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

        // 現在のPRIMARY列を確認
        $pk = DB::selectOne("
            SELECT k.COLUMN_NAME AS col
              FROM information_schema.table_constraints t
              JOIN information_schema.key_column_usage k
                ON k.constraint_name = t.constraint_name
               AND k.table_schema = t.table_schema
               AND k.table_name   = t.table_name
             WHERE t.table_schema = DATABASE()
               AND t.table_name   = 't_committee_kinds'
               AND t.constraint_type = 'PRIMARY'
             LIMIT 1
        ");
        $pkCol = $pk->col ?? null;

        // もし id が AUTO_INCREMENT なら先に外す（現在は外れている想定だが冪等に）
        if (Schema::hasColumn('t_committee_kinds', 'id')) {
            $idCol = DB::selectOne("
                SELECT EXTRA FROM information_schema.COLUMNS
                 WHERE TABLE_SCHEMA = DATABASE()
                   AND TABLE_NAME   = 't_committee_kinds'
                   AND COLUMN_NAME  = 'id'
                 LIMIT 1
            ");
            $extra = strtolower($idCol->EXTRA ?? '');
            if (strpos($extra, 'auto_increment') !== false) {
                DB::statement("ALTER TABLE t_committee_kinds MODIFY `id` BIGINT UNSIGNED NOT NULL");
            }
        }

        // PRIMARY が committeekinds_id でなければ付け替え（現状は既にそうなのでスキップされるはず）
        if ($pkCol !== 'committeekinds_id') {
            DB::statement("
                ALTER TABLE t_committee_kinds
                  DROP PRIMARY KEY,
                  ADD PRIMARY KEY (`committeekinds_id`)
            ");
        }

        // committeekinds_id を AUTO_INCREMENT 化（PRIMARY であればOK）
        DB::statement("ALTER TABLE t_committee_kinds MODIFY `committeekinds_id` INT UNSIGNED NOT NULL AUTO_INCREMENT");

        // 旧 id 列は不要なら削除（存在時のみ）
        if (Schema::hasColumn('t_committee_kinds', 'id')) {
            DB::statement("ALTER TABLE t_committee_kinds DROP COLUMN `id`");
        }

        // 論理削除運用のための複合インデックス（無ければ作成）
        $hasIdx = DB::selectOne("
            SELECT COUNT(1) AS c
              FROM information_schema.statistics
             WHERE table_schema = DATABASE()
               AND table_name   = 't_committee_kinds'
               AND index_name   = 'idx_committee_name_delflg'
        ");
        if ((int)($hasIdx->c ?? 0) === 0) {
            DB::statement("CREATE INDEX idx_committee_name_delflg ON t_committee_kinds (committeekindsname, del_flg)");
        }
    }

    public function down(): void
    {
        // 影響最小のロールバック（複合インデックスだけ戻す）
        $hasIdx = DB::selectOne("
            SELECT COUNT(1) AS c
              FROM information_schema.statistics
             WHERE table_schema = DATABASE()
               AND table_name   = 't_committee_kinds'
               AND index_name   = 'idx_committee_name_delflg'
        ");
        if ((int)($hasIdx->c ?? 0) > 0) {
            DB::statement("DROP INDEX idx_committee_name_delflg ON t_committee_kinds");
        }
    }
};
