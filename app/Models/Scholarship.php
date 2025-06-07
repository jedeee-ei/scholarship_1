<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Scholarship extends Model
{
    protected $fillable = [
        'name',
        'type',
        'semester',
        'academic_year',
        'description'
    ];
}
