<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TLicensekinds extends Model
{
    use HasFactory;
    protected $table = "t_licensekinds";
    protected $primaryKey = 'id';
}
