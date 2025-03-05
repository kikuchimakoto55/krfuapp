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
        'email', 'password'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
