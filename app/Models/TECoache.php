<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TECoache extends Model
{
    use HasFactory;
    protected $table = "t_e_coaches";
    protected $primaryKey = 'id';
}
