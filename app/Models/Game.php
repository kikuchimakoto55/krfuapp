<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $table = 't_games'; // ★テーブル名をt_gamesに指定
    protected $primaryKey = 'game_id'; // ★主キーがgame_idの場合
    protected $guarded = ['game_id']; // ★game_id以外は一括代入OK
}
