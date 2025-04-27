<?php

// app/Models/TournamentResult.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TournamentResult extends Model
{
    protected $table = 't_tournament_results';
    protected $primaryKey = 'result_id';

    protected $fillable = [
        'tournament_id',
        'division_order',
        'division_name',
        'rank_label',
        'team_id',
        'document_path',
        'report',
    ];
    
}
