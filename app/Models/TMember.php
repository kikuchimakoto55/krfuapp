<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class TMember extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 't_members'; // 🔥 `users` ではなく `t_members` を使用
    protected $primaryKey = 'member_id'; // ← 🔑 主キーを明示
    public $incrementing = true; // ← 主キーが自動増分の場合
    protected $keyType = 'int'; // ← 主キーの型が整数

    protected $fillable = [
        'name', 'email', 'password'
    ];

    protected $hidden = [
        'password', 'remember_token'
    ];
}
