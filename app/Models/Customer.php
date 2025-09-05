<?php

namespace App\Models;

use App\Enums\CustomerType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
     use SoftDeletes;

    protected $fillable = [
        'name','type','email','phone','country_code','city',
        'preferred_language','source','marketing_opt_in','notes',
    ];
    protected $casts = ['marketing_opt_in' => 'boolean'];

    public function travelers() { return $this->hasMany(Traveler::class); }
    public function bookings() { return $this->hasMany(Booking::class); }

    // 1–1 profiles
    public function individualProfile()
    {
        return $this->hasOne(IndividualProfile::class);
    }

    public function companyProfile()
    {
        return $this->hasOne(CompanyProfile::class);
    }

    // Example scopes
    public function scopeIndividuals($q)
    {
        return $q->where('type', CustomerType::INDIVIDUAL);
    }

    public function scopeCompanies($q)
    {
        return $q->where('type', CustomerType::COMPANY);
    }
}
