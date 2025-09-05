<?php

namespace App\Models;

use App\Enums\HotelType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hotel extends Model
{
    protected $fillable = [
        'name',
        'address',
        'category',     // 1..5
        'city_id',
        'type',         // enum-backed
        'description',
        'images',
        'phone',
        'email',
        'company_id',
    ];

    protected $casts = [
        'images'   => 'array',
        'category' => 'integer',
        'type'     => HotelType::class,   // << enum cast
    ];

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class, 'amenity_hotel')
            ->where('scope', 'hotel');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
