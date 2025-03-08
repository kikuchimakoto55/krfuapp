<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TAuthoritykind extends Model
{
    use HasFactory;

    protected $table = 't_authoritykinds'; // テーブル名

    protected $primaryKey = 'id'; // プライマリキー

    public $timestamps = false; // created_at, updated_at を無効化

    protected $fillable = [
        'authoritykinds_id',
        'authoritykindsname',
        'registration_date',
        'update_date',
        'del_flg'
    ];
}
