<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Traveler extends Model
{
   protected $fillable = [
        'customer_id','first_name','last_name','date_of_birth','nationality',
        'passport_number','passport_expires_at','gender','notes',
    ];
    protected $casts = [
        'date_of_birth' => 'date',
        'passport_expires_at' => 'date',
    ];

    public function customer() { return $this->belongsTo(Customer::class); }

    public function getFullNameAttribute(): string
    {
        return trim(($this->first_name ?? '').' '.($this->last_name ?? ''));
    }
}
