<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTTeamsTable extends Migration
{
    public function up()
    {
        Schema::create('t_teams', function (Blueprint $table) {
            $table->id(); // ← 自動採番されるユニークな主キー
            $table->unsignedSmallInteger('year'); // ← 追加された年度
            $table->string('team_id', 20); // ← チーム登録番号（ユニークでない）
            $table->string('team_name');
            $table->string('representative_name');
            $table->string('representative_kana');
            $table->string('representative_tel');
            $table->string('representative_email');
            $table->unsignedInteger('male_members')->default(0);
            $table->unsignedInteger('female_members')->default(0);
            $table->string('medical_supporter')->nullable();
            $table->string('jrfu_coach')->nullable();
            $table->string('safety_lecturer')->nullable();
            $table->unsignedTinyInteger('category');
            $table->unsignedTinyInteger('status')->default(1); // 有効 or 無効
            $table->unsignedTinyInteger('annual_fee_flg')->default(0); // 年会費納入完了
            $table->unsignedTinyInteger('individual_entry_flg')->default(0); // 個人登録完了
            $table->unsignedTinyInteger('team_entry_flg')->default(0); // チーム登録完了
            $table->timestamps(); // 登録日時・更新日時（必要なら）
        });
    }

    public function down()
    {
        Schema::dropIfExists('t_teams');
    }
}
