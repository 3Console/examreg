<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitClass extends Model
{
    protected $fillable = [
        'subject',
        'class_code',
        'lecturer',
    ];
}
