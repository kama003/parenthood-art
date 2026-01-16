<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donor extends Model
{
    protected $fillable = [
        'donor_number',
        'age',
        'aadhaar_number',
        'blood_group',
        'status',
        'eye_color',
        'hair_color',
        'body_structure',
        'complexion',
    ];

    protected $casts = [
        'age' => 'integer',
    ];
}
