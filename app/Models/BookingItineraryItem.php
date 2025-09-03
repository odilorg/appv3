<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingItineraryItem extends Model
{
    protected $fillable = [
        'booking_id','tour_itinerary_item_id','date','type','sort_order','title','description',
        'planned_start_time','planned_duration_minutes','meta'
    ];

    protected $casts = [
        'date' => 'date',
        'meta' => 'array',
    ];

    public function booking() { return $this->belongsTo(Booking::class); }
    public function sourceTourItem() { return $this->belongsTo(ItineraryItem::class, 'tour_itinerary_item_id'); }
}
