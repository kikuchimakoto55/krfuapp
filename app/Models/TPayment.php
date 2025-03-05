<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TPayment extends Model
{
    use HasFactory;
    protected $table = "t_payments";
    protected $primaryKey = 'id';
}
