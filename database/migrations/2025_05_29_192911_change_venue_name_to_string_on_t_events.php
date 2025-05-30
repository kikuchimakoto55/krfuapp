<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('t_events', function (Blueprint $table) {
        $table->string('venue_name', 100)->change(); // 長さは適宜調整
    });
}

public function down()
{
    Schema::table('t_events', function (Blueprint $table) {
        $table->integer('venue_name')->change(); // 元に戻す場合
    });
}
};
