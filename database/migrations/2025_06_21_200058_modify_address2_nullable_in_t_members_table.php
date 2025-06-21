<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyAddress2NullableInTMembersTable extends Migration
{
    public function up(): void
    {
        Schema::table('t_members', function (Blueprint $table) {
            $table->string('address2')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('t_members', function (Blueprint $table) {
            $table->string('address2')->nullable(false)->change(); // 元が NOT NULL だった場合に戻す
        });
    }
};