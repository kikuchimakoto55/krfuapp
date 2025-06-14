<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $table = 't_teams'; // テーブル名
    protected $primaryKey = 'id'; // 主キー
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
    'year', 'team_id', 'team_name', 'representative_name',
    'representative_kana', 'representative_tel', 'representative_email',
    'male_members', 'female_members', 'medical_supporter', 'jrfu_coach',
    'safety_lecturer', 'category', 'status', 'annual_fee_flg',
    'individual_entry_flg', 'team_entry_flg'
];
}
