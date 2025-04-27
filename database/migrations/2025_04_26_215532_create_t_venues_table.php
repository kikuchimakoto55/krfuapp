<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTVenuesTable extends Migration
{
    public function up()
    {
        Schema::create('t_venues', function (Blueprint $table) {
            $table->bigIncrements('venue_id');      // 会場ID
            $table->string('venue_name');            // 会場名
            $table->string('zip', 8)->nullable();    // 郵便番号（文字列で8桁）
            $table->string('address')->nullable();   // 住所
            $table->string('tel', 15)->nullable();   // 電話番号
            $table->string('mapurl')->nullable();    // 地図URL
            $table->string('hpurl')->nullable();     // 会場HP
            $table->tinyInteger('parking')->default(0); // 駐車場有無 0:無, 1:有
            $table->string('parking_number')->nullable(); // 駐車可能台数
            $table->text('remarks')->nullable();     // 特筆事項
            $table->timestamps();                    // created_at, updated_at
            $table->tinyInteger('del_flg')->default(0); // 削除フラグ
        });
    }

    public function down()
    {
        Schema::dropIfExists('t_venues');
    }
}
