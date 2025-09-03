<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Hotel extends Model
{
     protected $fillable = ['name',
     'address',
     'category',
     'city_id',
     'type',
     'description',
      'images',
    'description',
    'phone',
    'email',
    'images',
    'company_id'
    ];

    protected $casts = [
        'images' => 'array',
    ];

     public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }
     public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }
    public function hotelRooms()
    {
        return $this->hasMany(Room::class);
    }

    public function company(): BelongsTo
{
    return $this->belongsTo(Company::class);
}
}
