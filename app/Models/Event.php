<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $table = 't_events';
    protected $primaryKey = 'event_id';

    protected $fillable = [
        'event_name',
        'event_date',
        'event_opentime',
        'weather',
        'temperature',
        'venue_name',
        'event_year',
        'event_kinds',
        'event_overview',
        'del_flg',
    ];

    public $timestamps = true;
}