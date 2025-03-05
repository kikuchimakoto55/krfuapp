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
        Schema::create('t_coaches', function (Blueprint $table) {
            $table->bigIncrements("id")->comment('システムID')->nullable(false);
            $table->integer("member_id")->comment('会員番号')->nullable(false)->unique();
            $table->smallInteger('c_categorykinds_id')->comment('指導員種別ID')->nullable(false);
            $table->string('c_categorykindsname',20)->comment('指導員種別名称')->nullable(false);
            $table->string('email',100)->comment('指導員メールアドレス')->nullable(false)->unique();
            $table->text('remarks')->comment('備考')->nullable(true);
            $table->datetime('registration_date', $precision = 0)->comment('登録日')->nullable(false);
            $table->smallInteger('classification')->comment('所属区分')->nullable(true);
            $table->integer('referee_id')->comment('レフリー会員番号')->nullable(true);
            $table->string('password')->comment('パスワード')->nullable(false);
            $table->datetime('login_date', $precision = 0)->comment('最終ログイン日時')->nullable(false);
            $table->datetime('update_date', $precision = 0)->comment('更新日')->nullable(false);
            $table->smallInteger('del_flg')->comment('削除フラグ')->nullable(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_coach');
    }
};
