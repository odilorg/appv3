<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Restaurant extends Model
{
    protected $fillable = [
        'name','address','city_id','company_id','cuisine','price_level',
        'is_active','phone','email','website','opening_hours','images',
    ];

    protected $casts = [
        'is_active'     => 'boolean',
        'opening_hours' => 'array',
        'images'        => 'array',
    ];

    public function city(): BelongsTo { return $this->belongsTo(City::class); }
    public function company(): BelongsTo { return $this->belongsTo(Company::class); }
    public function meals()
{
    return $this->hasMany(\App\Models\RestaurantMeal::class);
}
}
