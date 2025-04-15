<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tournament extends Model
{
    use HasFactory;

    protected $table = 't_tournaments'; // テーブル名を指定
    protected $primaryKey = 'tournament_id'; // 主キーを指定
    public $incrementing = true; // tournament_id を自動採番
    protected $keyType = 'int'; // 主キーの型

    protected $fillable = [
        'categoly', 
        'year', 
        'event_period_start', 
        'event_period_end',
        'name', 
        'divisionflg', 
        'divisionname', 
        'divisionid',
        'publishing', 
        'registration_date', 
        'update_date', 
        'del_flg',
        'divisions',
    ];

    public $timestamps = false; // Laravelの created_at, updated_at を使わない
}

