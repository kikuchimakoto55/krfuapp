<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TCoach extends Model
{
    use HasFactory;
    protected $table = "t_coaches";
    protected $primaryKey = 'id';
}
