<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class APositionKind extends Model
{
    protected $table = 't_a_positionkinds';
    protected $primaryKey = 'a_positionkinds_id';
    public $timestamps = false;

    protected $fillable = [
        'a_positionkindskindsname',
        'registration_date',
        'update_date',
        'del_flg',
    ];

    protected $casts = [
        'registration_date' => 'datetime',
        'update_date' => 'datetime',
        'del_flg' => 'integer',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('not_deleted', function (Builder $q) {
            $q->where('del_flg', 0);
        });
    }
}
