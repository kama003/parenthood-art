<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sample extends Model
{
    protected $fillable = [
        'sample_id',
        'donor_id',
        'couple_id',
        'user_id',
        'blood_group',
        'vials_count',
        'freeze_date',
        'expiry_date',
        'status',
    ];

    protected $casts = [
        'freeze_date' => 'date',
        'expiry_date' => 'date',
        'vials_count' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function donor()
    {
        return $this->belongsTo(Donor::class);
    }

    public function couple()
    {
        return $this->belongsTo(Couple::class);
    }
}
