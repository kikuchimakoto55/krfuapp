<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HCredential extends Model
{
    use HasFactory;

    protected $table = 't_h_credentials'; // テーブル名明示
    protected $primaryKey = 'h_credentials_id'; // 主キーを明示
    public $incrementing = true; // AUTO_INCREMENTあり
    protected $keyType = 'int';

    // Laravelのタイムスタンプ created_at/updated_at を使う場合
    public $timestamps = true;

    protected $fillable = [
        'member_id',
        'license_id',
        'licensekindsname',
        'acquisition_date',
        'expiration_date',
        'valid_flg',
        'del_flg',
    ];

    //  会員とのリレーション（belongsTo）
    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id', 'member_id');
    }

    //  資格マスタとのリレーション（belongsTo）
    public function license()
    {
        return $this->belongsTo(License::class, 'license_id', 'license_id');
    }

    
}
