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
        Schema::create('t_licenses', function (Blueprint $table) {
            $table->bigIncrements("id")->comment('システムID')->nullable(false);
            $table->integer("license_id")->comment('資格ID')->nullable(false)->unique();
            $table->integer('licensekinds_id')->comment('資格種別ID')->nullable(false);
            $table->string('licensekindsname',50)->comment('資格種別名称')->nullable(false);
            $table->date('year')->comment('有効期限')->nullable(false);
            $table->datetime('expiredate', $precision = 0)->comment('失効日')->nullable(false);
            $table->text('participation_conditions')->comment('受講条件')->nullable(true);
            $table->text('requirements')->comment('資格要項')->nullable(true);
            $table->string('requirements_url')->comment('要項URL')->nullable(true);
            $table->text('management_organization')->comment('登録先組織')->nullable(true);
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
        Schema::dropIfExists('t_license');
    }
};
