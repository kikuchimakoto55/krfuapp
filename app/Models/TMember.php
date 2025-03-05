<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class TMember extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $table = 't_members'; // 🔥 `users` ではなく `t_members` を使用

    protected $fillable = [
        'name', 'email', 'password'
    ];

    protected $hidden = [
        'password', 'remember_token'
    ];
}
