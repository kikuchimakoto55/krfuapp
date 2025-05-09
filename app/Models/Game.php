<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $table = 't_games'; // テーブル名を明示
    protected $primaryKey = 'game_id'; // 主キー
    protected $guarded = ['game_id']; // 一括代入から除外
    protected $dates = ['game_date'];

    // 🔽 以下、クラスの中にリレーションを書く

    public function team1()
    {
        return $this->belongsTo(Team::class, 'team1_id', 'team_id');
    }

    public function team2()
    {
        return $this->belongsTo(Team::class, 'team2_id', 'team_id');
    }

    public function venue()
    {
    return $this->belongsTo(Venue::class, 'venue_id');
    }

    public function score()
    {
        return $this->hasOne(Score::class, 'game_id', 'game_id');
    }

    

}