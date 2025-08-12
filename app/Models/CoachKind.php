<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Coach;

class CoachKind extends Model
{
    protected $table = 't_coach_kinds';
    protected $primaryKey = 'c_categorykinds_id';
    public $timestamps = false; // registration_date / updated_at を手動運用
    protected $keyType = 'int';  // ★ 明示
    public $incrementing = true; // ★ 明示（AUTO_INCREMENT）

    protected $fillable = [
        'c_categorykindsname',
        'registration_date',
        'updated_at',
        'del_flg',
    ];

    protected $casts = [          // ★ 型キャスト（取り回し安定）
        'del_flg' => 'integer',
        'registration_date' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $attributes = [     // ★ 既定値（明示）
        'del_flg' => 0,
    ];

    public function coaches()
    {
        return $this->hasMany(Coach::class, 'c_categorykinds_id', 'c_categorykinds_id');
    }
}