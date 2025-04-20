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
        Schema::create('t_members', function (Blueprint $table) {
            $table->integer("member_id")->autoIncrement()->comment('会員番号');
            $table->smallInteger('grade_category')->comment('学年カテゴリ')->nullable(false);
            $table->string("username_sei",20)->comment('氏名 (姓)')->nullable(false);
            $table->string("username_mei",20)->comment('氏名 (名)')->nullable(false);
            $table->string("username_kana_s",20)->comment('氏名 (姓)カナ')->nullable(false);
            $table->string("username_kana_m",20)->comment('氏名 (名)カナ')->nullable(false);
            $table->string("username_en_s",20)->comment('氏名 (姓)英')->nullable(false);
            $table->string("username_en_m",20)->comment('氏名 (名)英')->nullable(false);
            $table->smallInteger('sex')->comment('性別')->nullable(false);
            $table->date("birthday")->comment('生年月日')->nullable(false);
            $table->smallInteger('height')->comment('身長（cm）')->nullable(true);
            $table->smallInteger('weight')->comment('体重（kg）')->nullable(true);
            $table->smallInteger('blood_type')->comment('血液型')->nullable(true);
            $table->string('zip',7)->comment('郵便番号')->nullable(false);
            $table->string('address1',10)->comment('都道府県')->nullable(false);
            $table->string('address2',50)->comment('市区町村')->nullable(false);
            $table->string('address3',50)->comment('住所３')->nullable(true);
            $table->string('enrolled_school',50)->comment('住在籍学校・園名')->nullable(true); 
            $table->string('guardian_name',20)->comment('保護者氏名')->nullable(false); 
            $table->string('guardian_email',100)->comment('保護者メールアドレス')->nullable(false)->unique();
            $table->unsignedBigInteger('guardian_tel')->comment('保護者電話番号')->nullable(false);
            $table->smallInteger('relationship')->comment('続柄')->nullable(false);
            $table->string('emergency_name1',20)->comment('緊急連絡先・氏名')->nullable(false);
            $table->string('emergency_email1',100)->comment('緊急連絡先・メールアドレス')->nullable(false)->unique();
            $table->unsignedBigInteger('emergency_tel1')->comment('緊急連絡先・電話番号')->nullable(false);
            $table->string('email',100)->comment('本人メールアドレス')->nullable(true)->unique();
            $table->unsignedBigInteger('tel')->comment('本人電話番号')->nullable(true);
            $table->text('remarks')->comment('備考')->nullable(true);
            $table->datetime('registration_date')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('登録日');
            $table->smallInteger('classification')->comment('所属区分')->nullable(true);
            $table->integer('membershipfee_conf')->comment('保険登録番号')->nullable(true);
            $table->integer('association_id')->comment('協会登録番号')->nullable(true);
            $table->smallInteger('status')->comment('在籍状況')->nullable(false);
            $table->integer('graduation_year')->nullable()->comment('卒業年度');
            $table->string('password')->comment('パスワード')->nullable(false);
            $table->smallInteger('authoritykinds_id')->comment('権限種別ID')->nullable(true);
            $table->string('authoritykindsname',30)->comment('権限種別名称')->nullable(true);
            $table->datetime('login_date')->default(DB::raw('CURRENT_TIMESTAMP'))->comment('最終ログイン日時');
            $table->datetime('update_date')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'))->comment('更新日');
            $table->smallInteger('coach_flg')->comment('指導員フラグ')->nullable(false);
            $table->smallInteger('del_flg')->comment('削除フラグ')->nullable(false)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_members');
    }
};
