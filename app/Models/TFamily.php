<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TFamily extends Model
{
    use HasFactory;
    protected $table = "t_families";
    protected $primaryKey = 'id';
}
