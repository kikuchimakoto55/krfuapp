<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoachKind extends Model
{
    protected $table = 't_coach_kinds';
    protected $primaryKey = 'c_categorykinds_id';
    public $timestamps = false; // registration_date / updated_at を手動運用

    protected $fillable = [
        'c_categorykindsname',
        'registration_date',
        'updated_at',
        'del_flg',
    ];

    public function coaches()
    {
        return $this->hasMany(Coach::class, 'c_categorykinds_id', 'c_categorykinds_id');
    }
}