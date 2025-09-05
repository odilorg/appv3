<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BookingItineraryItem extends Model
{
    protected $fillable = [
        'booking_id',
        'date',            // optional: the day this item occurs
        'title',           // optional label
        'notes',
        'assignable_type', // e.g. App\Models\Hotel, App\Models\Guide, etc.
        'assignable_id',
        'sort_order',      // if you order items
        'start_time',      // optional
        'end_time',        // optional
        // 'check_in', 'check_out' // if you store hotel dates per-item instead of using booking dates
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function assignable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Rooms attached to this itinerary item (only meaningful when assignable = Hotel)
     */
    public function roomAssignments(): HasMany
    {
        return $this->hasMany(BookingItineraryItemRoom::class, 'booking_itinerary_item_id');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(BookingItineraryItemAssignment::class, 'booking_itinerary_item_id');
    }
}
