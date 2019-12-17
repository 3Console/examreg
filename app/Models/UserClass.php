<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserClass extends Model
{
    protected $fillable = [
        'user_id',
        'unit_class_id',
        'is_valid',
    ];
}
