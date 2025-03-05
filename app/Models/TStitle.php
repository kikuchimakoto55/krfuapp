<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TStitle extends Model
{
    use HasFactory;
    protected $table = "t_s_titles";
    protected $primaryKey = 'id';
}
