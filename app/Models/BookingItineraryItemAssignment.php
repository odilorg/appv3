<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookingItineraryItemAssignment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'booking_itinerary_item_id',
        'assignable_type',
        'assignable_id',
        'car_id',
        'room_id',
        'restaurant_meal_id',
        'role',
        'quantity',
        'cost',
        'currency',
        'status',
        'start_time',
        'end_time',
        'notes',
    ];

    protected $casts = [
        'booking_itinerary_item_id' => 'integer',
        'assignable_id'             => 'integer',
        'car_id'                    => 'integer',
        'room_id'                   => 'integer',
        'restaurant_meal_id'        => 'integer',
        'quantity'                  => 'integer',
        'cost'                      => 'decimal:2',
    ];

    // Inverse of BookingItineraryItem::assignments()
    public function item(): BelongsTo
    {
        return $this->belongsTo(BookingItineraryItem::class, 'booking_itinerary_item_id');
    }

    // Polymorphic target (Guide / Driver / Hotel / Restaurant)
    public function assignable(): MorphTo
    {
        return $this->morphTo();
    }
}
