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
        Schema::create('t_authoritykinds', function (Blueprint $table) {
            $table->bigIncrements("id")->comment('システムID')->nullable(false);
            $table->integer("authoritykinds_id")->comment('権限種別ID')->nullable(false)->unique();
            $table->string('authoritykindsname',100)->comment('権限種別名称')->nullable(true);
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
        Schema::dropIfExists('t_authoritykinds');
    }
};
