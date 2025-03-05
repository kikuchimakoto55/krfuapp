<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TVenue extends Model
{
    use HasFactory;
    protected $table = "t_venues";
    protected $primaryKey = 'id';
}
