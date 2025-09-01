<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingItemSupplier extends Model
{
     protected $fillable = [
        'booking_itinerary_item_id','supplier_type','supplier_id','role',
        'qty','unit_price','currency','status','notes'
    ];

    public function item() { return $this->belongsTo(BookingItineraryItem::class, 'booking_itinerary_item_id'); }

    public function supplier() { return $this->morphTo(); } // Driver, Guide, Vehicle, Hotel, etc.
}
