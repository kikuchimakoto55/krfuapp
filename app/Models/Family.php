<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Family extends Model
{
    // 対応するテーブル名
    protected $table = 't_families';

    // 主キーのカラム名（通常は id なので省略可能）
    protected $primaryKey = 'id';

    // タイムスタンプのカラム名が created_at / updated_at でない場合は false に
    public $timestamps = false;

    // 登録・更新可能なカラム（ホワイトリスト）
    protected $fillable = [
        'member_id',
        'family_id',
        'relationship',
        'registration_date',
        'update_date',
        'del_flg'
    ];

    // 論理削除用のフラグ名が "deleted_at" でないため、SoftDeletes は使わず自前で制御
}
