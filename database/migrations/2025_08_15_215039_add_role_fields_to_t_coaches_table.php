<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('t_coaches', function (Blueprint $table) {
            // 1=coachKinds, 2=committeeKinds, 3=aPositionKinds
            $table->unsignedTinyInteger('role_type')
                  ->default(1)
                  ->comment('1=coachKinds,2=committeeKinds,3=aPositionKinds')
                  ->after('member_id');

            $table->unsignedInteger('role_kinds_id')
                  ->default(0)
                  ->after('role_type');

            $table->string('role_kindsname', 100)
                  ->nullable()
                  ->after('role_kinds_id');

            // 重複抑止（del_flg 含む）
            $table->unique(
                ['member_id', 'role_type', 'role_kinds_id', 'del_flg'],
                'uq_member_role_kind_delflg'
            );
        });

        // 既存データの後方互換：c_categorykinds_* から role_* に反映
        DB::statement("
            UPDATE t_coaches
            SET
                role_type = 1,
                role_kinds_id = COALESCE(c_categorykinds_id, 0),
                role_kindsname = CASE
                    WHEN role_kindsname IS NULL OR role_kindsname = '' THEN c_categorykindsname
                    ELSE role_kindsname
                END
        ");

        // 名称が未設定の場合はマスタから補完
        DB::statement("
            UPDATE t_coaches tc
            JOIN t_coach_kinds ck
              ON ck.c_categorykinds_id = tc.role_kinds_id
            SET tc.role_kindsname = ck.c_categorykindsname
            WHERE tc.role_type = 1
              AND (tc.role_kindsname IS NULL OR tc.role_kindsname = '')
              AND tc.role_kinds_id <> 0
        ");
    }

    public function down(): void
    {
        Schema::table('t_coaches', function (Blueprint $table) {
            $table->dropUnique('uq_member_role_kind_delflg');
            $table->dropColumn(['role_type', 'role_kinds_id', 'role_kindsname']);
        });
    }
};
