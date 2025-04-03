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
        Schema::create('t_venues', function (Blueprint $table) {
            $table->bigIncrements("id")->comment('システムID')->nullable(false);
            $table->integer("venue_id")->comment('会場管理ID')->nullable(false)->unique();
            $table->string('venue_name',70)->comment('会場名')->nullable(false);
            $table->string('zip',7)->comment('郵便番号')->nullable(false);
            $table->string('address',70)->comment('会場住所')->nullable(false);
            $table->unsignedBigInteger('tel')->comment('電話番号')->nullable(false);
            $table->string('mapurl')->comment('map URL')->nullable(true);
            $table->string('hpurl')->comment('会場HP')->nullable(true);
            $table->smallInteger('parking')->comment('駐車場有無')->nullable(true);
            $table->string('parking_number',5)->comment('駐車可能台数')->nullable(true);
            $table->text('remarks')->comment('特筆事項')->nullable(true);
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
    Schema::dropIfExists('t_venues');
    }
};
