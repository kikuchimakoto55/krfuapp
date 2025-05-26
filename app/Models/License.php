<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class License extends Model
{
    protected $primaryKey = 'license_id'; // ← これが必須！
    public $incrementing = true; // 自動採番する
    protected $keyType = 'int'; // 主キーの型
    protected $table = 't_licenses';

    protected $fillable = [
    'licensekinds_id', 'licensekindsname', 'valid_period', // ← 修正済み
    'participation_conditions', 'requirements', 'requirements_url',
    'management_organization', 'del_flg',
    ];

    protected $dates = ['valid_period', 'created_at', 'updated_at'];

    protected $casts = [
    'valid_period' => 'integer', // ← 数値型としてキャスト
];
}