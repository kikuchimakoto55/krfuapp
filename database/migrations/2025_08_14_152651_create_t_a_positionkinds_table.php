<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('t_a_positionkinds', function (Blueprint $table) {
            $table->increments('a_positionkinds_id'); // PK AUTO_INCREMENT
            $table->string('a_positionkindskindsname', 100); // 名称
            $table->dateTime('registration_date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->dateTime('update_date')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
            $table->tinyInteger('del_flg')->default(0);

            $table->index(['a_positionkindskindsname', 'del_flg'], 'idx_apositionkind_name_delflg');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('t_a_positionkinds');
    }
};
