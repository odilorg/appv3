<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IndividualProfile extends Model
{
     protected $primaryKey = 'customer_id';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'customer_id',
        'dob',
        'nationality',
        'passport_number',
        'passport_expiry',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
