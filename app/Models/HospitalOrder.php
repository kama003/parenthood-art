<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HospitalOrder extends Model
{
    protected $fillable = [
        'hospital_name',
        'couple_id',
        'sample_id',
        'user_id',
        'vials_count',
        'notes',
        'status',
        'aadhaar_file_path',
        'declaration_accepted',
    ];

    protected $casts = [
        'vials_count' => 'integer',
    ];

    public function user()
    {
         return $this->belongsTo(User::class);
    }

    public function couple()
    {
        return $this->belongsTo(Couple::class);
    }

    public function sample()
    {
        return $this->belongsTo(Sample::class);
    }
}
