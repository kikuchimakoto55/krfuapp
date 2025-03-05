<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TLicense extends Model
{
    use HasFactory;
    protected $table = "t_licenses";
    protected $primaryKey = 'id';
}
