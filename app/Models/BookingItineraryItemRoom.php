<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingItineraryItemRoom extends Model
{
    protected $fillable = [
        'booking_itinerary_item_id',
        'room_id',
        'quantity',
        'rate',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'rate' => 'decimal:2',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(BookingItineraryItem::class, 'booking_itinerary_item_id');
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
}
