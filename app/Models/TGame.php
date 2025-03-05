<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TGame extends Model
{
    use HasFactory;
    protected $table = "t_games";
    protected $primaryKey = 'id';
}
