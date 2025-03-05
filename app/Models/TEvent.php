<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TEvent extends Model
{
    use HasFactory;
    protected $table = "t_events";
    protected $primaryKey = 'id';
}
