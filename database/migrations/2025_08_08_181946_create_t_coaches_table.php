<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('t_coaches', function (Blueprint $table) {
            $table->increments('coach_id')->comment('指導員情報ID');

            // ▼ ここを unsignedInteger から integer に変更（UNSIGNEDを外す）
            $table->integer('member_id')->comment('会員ID（t_members.member_id）');

            // t_coach_kinds 側が increments なのでこちらは UNSIGNEDのままでOK
            $table->unsignedInteger('c_categorykinds_id')->comment('指導員種別ID（t_coach_kinds.c_categorykinds_id）');

            $table->string('c_categorykindsname', 100)->nullable()->comment('指導員種別名称（冗長保持・任意）');
            $table->text('remarks')->nullable()->comment('備考');
            $table->timestamp('registration_date')->useCurrent()->comment('登録日');
            $table->string('referee_id', 50)->nullable()->comment('レフリー会員番号');
            $table->dateTime('login_date')->nullable()->comment('最終ログイン日時');
            $table->timestamp('update_date')->useCurrent()->useCurrentOnUpdate()->comment('更新日');
            $table->tinyInteger('del_flg')->default(0)->comment('削除フラグ(0:有効,1:削除)');

            $table->unique(['member_id', 'c_categorykinds_id'], 'uk_member_kind');
            $table->index('member_id', 'idx_coaches_member');
            $table->index('c_categorykinds_id', 'idx_coaches_kind');
            $table->index('del_flg', 'idx_coaches_delflg');

            $table->foreign('member_id', 'fk_coaches_member')
                ->references('member_id')->on('t_members')
                ->onDelete('cascade')->onUpdate('restrict');

            $table->foreign('c_categorykinds_id', 'fk_coaches_kind')
                ->references('c_categorykinds_id')->on('t_coach_kinds')
                ->onDelete('restrict')->onUpdate('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_coaches');
    }
};