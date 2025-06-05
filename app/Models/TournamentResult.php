<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TournamentResult extends Model
{
    use HasFactory;

    protected $table = 't_tournament_results';
    protected $primaryKey = 'result_id';

    public $timestamps = true; // ← 追加してください

    protected $fillable = [
        'tournament_id',
        'division_order',
        'division_name',
        'rank_order',
        'rank_label',
        'team_id',
        'report',
        'document_path',
        'del_flg',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class, 'team_id', 'id');
    }
}