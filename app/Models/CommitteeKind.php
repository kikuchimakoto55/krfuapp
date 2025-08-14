<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommitteeKind extends Model
{
    protected $table = 't_committee_kinds';
    protected $primaryKey = 'committeekinds_id';
    public $timestamps = false; // registration_date / updated_at を手動運用

    protected $fillable = [
        'committeekindsname',
        'registration_date',
        'updated_at',
        'del_flg',
    ];

    protected $attributes = [
        'del_flg' => 0,
    ];

    protected $casts = [
        'committeekinds_id' => 'integer',
        'del_flg'           => 'integer',
        'registration_date' => 'datetime',
        'updated_at'        => 'datetime',
    ];
}