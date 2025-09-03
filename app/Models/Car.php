<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Car extends Model
{
    protected $fillable = [
        'image',
        'driver_id',
        'make', 
        'model',
        'year',
        'plate_number',
        'color',
        'vin',
        'seats',
        'is_active',
        'notes',
        'image',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }
}
