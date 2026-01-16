<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Couple extends Model
{
    protected $fillable = [
        'user_id',
        'registration_number',
        'partner_1_name',
        'partner_1_aadhaar_path',
        'partner_2_name',
        'partner_2_aadhaar_path',
        'contact_number',
        'email',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
