<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Member extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 't_members'; // テーブル名を指定

    protected $fillable = [
        'grade_category',
        'username_sei',
        'username_mei',
        'username_kana_s',
        'username_kana_m',
        'sex',
        'username_en_s',
        'username_en_m',
        'birthday',
        'height',
        'weight',
        'blood_type',
        'zip',
        'address1',
        'address2',
        'address3',
        'enrolled_school',
        'guardian_name',
        'guardian_email',
        'guardian_tel',
        'relationship',
        'emergency_name1',
        'emergency_email1',
        'emergency_tel1',
        'email',
        'tel',
        'remarks',
        'classification',
        'membershipfee_conf',
        'association_id',
        'status',
        'graduation_year',
        'authoritykinds_id',
        'coach_flg',
        'password',
        'registration_date' // もし手動追加するならこれも
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
