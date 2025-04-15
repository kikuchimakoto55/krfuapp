<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('t_families', function (Blueprint $table) {
            $table->bigIncrements("id")->comment('システムID')->nullable(false);
            $table->integer("familymaster_id")->comment('家族管理ID')->nullable(false)->unique();
            $table->integer('member_id')->comment('会員番号')->nullable(false);
            $table->integer('family_id')->comment('家族ID')->nullable(false);
            $table->string('fname',50)->comment('家族氏名')->nullable(false);
            $table->smallInteger('relationship')->comment('続柄')->nullable(false);
            $table->datetime('registration_date', $precision = 0)->comment('登録日')->nullable(false);
            $table->datetime('update_date', $precision = 0)->comment('更新日')->nullable(false);
            $table->smallInteger('del_flg')->comment('削除フラグ')->nullable(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_families');
    }
};
