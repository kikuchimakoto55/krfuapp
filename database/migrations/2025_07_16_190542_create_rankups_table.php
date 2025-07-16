<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRankupsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rankups_table', function (Blueprint $table) {
            $table->id();
            $table->string('username_kana_s', 50)->comment('姓（カナ）');
            $table->string('username_kana_m', 50)->comment('名（カナ）');
            $table->string('sex', 10)->comment('性別');
            $table->string('birthday1', 4)->comment('生年');
            $table->string('birthday2', 2)->comment('生月');
            $table->string('birthday3', 2)->comment('生日');
            $table->boolean('rankup_flg')->default(0)->comment('年度更新済みフラグ 0=未処理, 1=処理済み');
            $table->timestamps(); // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rankups_table');
    }
};
