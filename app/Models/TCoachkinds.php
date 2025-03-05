<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TCoachkinds extends Model
{
    use HasFactory;
    protected $table = "t_coach_kinds";
    protected $primaryKey = 'id';
}
