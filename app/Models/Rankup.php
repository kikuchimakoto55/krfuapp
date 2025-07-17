<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rankup extends Model
{
    use HasFactory;

    protected $table = 'rankups_table';

    protected $fillable = [
        'username_kana_s',
        'username_kana_m',
        'sex',
        'birthday1',
        'birthday2',
        'birthday3',
        'rankup_flg',
    ];
}