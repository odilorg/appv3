<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RestaurantMeal extends Model
{
    protected $fillable = [
        'restaurant_id','name','type','price','currency',
        'per_person','is_active','sort_order','description',
    ];

    protected $casts = [
        'per_person' => 'boolean',
        'is_active'  => 'boolean',
        'price'      => 'decimal:2',
    ];

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }
}
