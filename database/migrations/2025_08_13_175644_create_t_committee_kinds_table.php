<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('t_committee_kinds', function (Blueprint $table) {
            $table->increments('committeekinds_id'); // UNSIGNED AI PK
            $table->string('committeekindsname', 100);
            $table->dateTime('registration_date')->useCurrent();
            $table->dateTime('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->tinyInteger('del_flg')->default(0);
            $table->index(['committeekindsname', 'del_flg'], 'idx_committee_name_delflg');

            $table->charset   = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('t_committee_kinds');
    }
};
