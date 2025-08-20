<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coach extends Model
{
    protected $table = 't_coaches';
    protected $primaryKey = 'coach_id';
    // t_coaches は registration_date / update_date を使用
    public $timestamps = true;
    const CREATED_AT = 'registration_date';
    const UPDATED_AT = 'update_date';

    // 役割種別
    public const ROLE_COACH     = 1; // coachKinds
    public const ROLE_COMMITTEE = 2; // committeeKinds
    public const ROLE_APOSITION = 3; // aPositionKinds

    protected $fillable = [
        'member_id',
        'role_type',
        'role_kinds_id',
        'role_kindsname',
        'remarks',
        'referee_id',
        'del_flg',

        // 後方互換
        'c_categorykinds_id',
        'c_categorykindsname',
    ];

    protected $casts = [
        'member_id'         => 'integer',
        'role_type'         => 'integer',
        'role_kinds_id'     => 'integer',
        'del_flg'           => 'integer',
        'registration_date' => 'datetime',
        'update_date'       => 'datetime',
    ];

    /* スコープ */
    public function scopeActive($q)
    {
        return $q->where('del_flg', 0);
    }

    public function scopeOfMember($q, int $memberId)
    {
        return $q->where('member_id', $memberId);
    }

    public function scopeOfRole($q, int $roleType, int $roleKindsId)
    {
        return $q->where('role_type', $roleType)
                 ->where('role_kinds_id', $roleKindsId);
    }
}
