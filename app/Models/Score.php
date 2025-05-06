<?php

// app/Models/Score.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Score extends Model
{
    protected $table = 't_scores';
    protected $primaryKey = 'score_id';
    public $incrementing = true;
    protected $guarded = ['id'];
    
    public $timestamps = false;

    public function game()
    {
        return $this->belongsTo(Game::class, 'game_id', 'game_id');
    }
}