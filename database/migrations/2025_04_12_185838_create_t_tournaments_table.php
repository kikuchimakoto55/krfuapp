<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
        {
        Schema::create('t_tournaments', function (Blueprint $table) {
            $table->bigIncrements('tournament_id'); // 主キー＆AUTO_INCREMENT
            $table->smallInteger('categoly');
            $table->string('year', 4); // ←西暦4桁の文字列で保持（例："2025"）
            $table->dateTime('event_period_start');
            $table->dateTime('event_period_end')->nullable();
            $table->string('name', 100);
            $table->smallInteger('divisionflg')->default(0);
            $table->string('divisionname', 100)->nullable();
            $table->integer('divisionid')->nullable();
            $table->json('divisions')->nullable();
            $table->smallInteger('publishing')->default(0);
            $table->dateTime('registration_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->dateTime('update_date')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
            $table->smallInteger('del_flg')->default(0);
        });
        }

    public function down(): void
    {
        Schema::dropIfExists('t_tournaments');
    }
};
