<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TTournament extends Model
{
    use HasFactory;
    protected $table = "t_tournaments";
    protected $primaryKey = 'id';
}
