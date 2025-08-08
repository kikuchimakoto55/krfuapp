<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('t_coach_kinds', function (Blueprint $table) {
            $table->increments('c_categorykinds_id')->unsigned()->comment('指導員種別ID');
            $table->string('c_categorykindsname', 100)->comment('指導員種別名称');
            $table->timestamp('registration_date')->useCurrent()->comment('登録日');
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate()->comment('更新日');
            $table->tinyInteger('del_flg')->default(0)->comment('削除フラグ(0:有効,1:削除)');
            $table->index('del_flg', 'idx_coach_kinds_delflg');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_coach_kinds');
    }
};