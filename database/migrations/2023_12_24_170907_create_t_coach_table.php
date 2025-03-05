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
        Schema::create('t_coach', function (Blueprint $table) {
            $table->bigIncrements("member_id")->comment('会員番号')->nullable(false);
            $table->smallInteger('c_categorykinds_id')->comment('指導員種別ID')->nullable(false);
            $table->string('c_categorykindsname',20)->comment('指導員種別名称')->nullable(false);
            $table->string("username_sei",20)->comment('氏名 (姓)')->nullable(false);
            $table->string("username_mei",20)->comment('氏名 (名)')->nullable(false);
            $table->string("username_kana_s",20)->comment('氏名 (姓)カナ')->nullable(false);
            $table->string("username_kana_m",20)->comment('氏名 (名)カナ')->nullable(false);
            $table->string("username_en_s",20)->comment('氏名 (姓)英')->nullable(false);
            $table->string("username_en_m",20)->comment('氏名 (名)英')->nullable(false);
            $table->smallInteger('sex')->comment('性別')->nullable(false);
            $table->date("birthday")->comment('生年月日')->nullable(false);
            $table->string('zip',7)->comment('郵便番号')->nullable(false);
            $table->string('address1',10)->comment('都道府県')->nullable(false);
            $table->string('address2',50)->comment('市区町村')->nullable(false);
            $table->string('address3',50)->comment('住所３')->nullable(true);
            $table->string('emergency_name1',20)->comment('緊急連絡先・氏名')->nullable(false);
            $table->string('emergency_email1',100)->comment('緊急連絡先・メールアドレス')->nullable(false)->unique();
            $table->unsignedBigInteger('emergency_tel1')->comment('緊急連絡先・電話番号')->nullable(false);
            $table->string('email',100)->comment('本人メールアドレス')->nullable(false)->unique();
            $table->string('email2',100)->comment('本人メールアドレス2')->nullable(true)->unique();
            $table->unsignedBigInteger('tel')->comment('本人電話番号')->nullable(false);
            $table->text('remarks')->comment('備考')->nullable(true);
            $table->datetime('registration_date', $precision = 0)->comment('登録日')->nullable(false);
            $table->smallInteger('classification')->comment('所属区分')->nullable(true);
            $table->integer('membershipfee_conf')->comment('保険登録番号')->nullable(true);
            $table->integer('association_id')->comment('協会登録番号')->nullable(true);
            $table->integer('referee_id')->comment('レフリー会員番号')->nullable(true);
            $table->smallInteger('committeekinds_id')->comment('委員会種別ID')->nullable(true);
            $table->string('committeekindsname',30)->comment('委員会種別名称')->nullable(true);
            $table->smallInteger('stitlekinds_id')->comment('スクール役職種別ID')->nullable(true);
            $table->string('stitlekindsname',30)->comment('スクール役職種別名称')->nullable(true);
            $table->smallInteger('a_positionkinds_id')->comment('協会役職種別ID')->nullable(true);
            $table->string('a_positionkindskindsname',30)->comment('協会役職種別名称')->nullable(true);
            $table->smallInteger('authoritykinds_id')->comment('権限種別ID')->nullable(true);
            $table->string('authoritykindsname',30)->comment('権限種別名称')->nullable(true);
            $table->string('password')->comment('パスワード')->nullable(false);
            $table->datetime('login_date', $precision = 0)->comment('最終ログイン日時')->nullable(false);
            $table->datetime('update_date', $precision = 0)->comment('更新日')->nullable(false);
            $table->smallInteger('del_flg')->comment('削除フラグ')->nullable(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_coach');
    }
};
