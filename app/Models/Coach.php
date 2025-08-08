<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coach extends Model
{
    protected $table = 't_coaches';
    protected $primaryKey = 'coach_id';
    public $timestamps = false; // registration_date / update_date を手動運用

    protected $fillable = [
        'member_id',
        'c_categorykinds_id',
        'c_categorykindsname',
        'remarks',
        'registration_date',
        'referee_id',
        'login_date',
        'update_date',
        'del_flg',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class, 'member_id', 'member_id');
    }

    public function kind()
    {
        return $this->belongsTo(CoachKind::class, 'c_categorykinds_id', 'c_categorykinds_id');
    }
}