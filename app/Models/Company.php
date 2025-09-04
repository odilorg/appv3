<?php

namespace App\Models;

use App\Models\Car;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    protected $fillable = [
        'name','address_street','address_city','phone','email','inn',
        'account_number','bank_name','bank_mfo','director_name','logo',
        'is_operator','license_number',
    ];

    protected $casts = [
        'is_operator' => 'boolean',
    ];

    public function cars(): HasMany
    {
        return $this->hasMany(Car::class);
    }
}
