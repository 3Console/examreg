<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = [
        'unit_class_id',
        'shift_id',
        'location_id',
        'date',
    ];
}
