<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venue extends Model
{
    use HasFactory;

    protected $table = 't_venues';   // テーブル名を明示的に指定
    protected $primaryKey = 'venue_id'; // 主キーを venue_id に設定

    protected $guarded = ['venue_id']; // venue_id以外は一括代入OK
}
