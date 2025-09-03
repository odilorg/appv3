<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingItineraryItemAssignment extends Model
{
    protected $fillable = [
        'booking_itinerary_item_id',
        'assignable_type', 'assignable_id',
        'role', 'quantity', 'cost', 'currency',
        'status', 'notes', 'start_time', 'end_time',
    ];

    public function bookingItem()
    {
        return $this->belongsTo(BookingItineraryItem::class, 'booking_itinerary_item_id');
    }

    public function assignable()
    {
        return $this->morphTo();
    }

    public function assignments()
{
    return $this->hasMany(BookingItineraryItemAssignment::class);
}

public function bookingAssignments()
{
    return $this->morphMany(\App\Models\BookingItineraryItemAssignment::class, 'assignable');
}
}
