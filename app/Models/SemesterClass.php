<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SemesterClass extends Model
{
    protected $fillable = [
        'semester_id',
        'unit_class_id',
    ];
}
