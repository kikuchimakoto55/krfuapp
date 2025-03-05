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
        Schema::create('t_h_credentials', function (Blueprint $table) {
            $table->bigIncrements("id")->comment('システムID')->nullable(false);
            $table->integer("h_credentials_id")->comment('保有資格ID')->nullable(false)->unique();
            $table->integer('member_id')->comment('会員番号')->nullable(false);
            $table->integer('license_id')->comment('資格ID')->nullable(false);
            $table->string('licensekindsname',100)->comment('資格種別名称')->nullable(false);
            $table->date('acquisition_date')->comment('取得日')->nullable(false);
            $table->date('expiration_date')->comment('期限日')->nullable(false);
            $table->smallInteger('valid_flg')->comment('有効フラグ')->nullable(false);
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
        Schema::dropIfExists('t_h_credentials');
    }
};
