<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BookingItineraryItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'booking_id','tour_itinerary_item_id','is_custom','is_locked','status',
        'date','type','title','description',
        'planned_start_time','planned_duration_minutes','meta',
    ];

    protected $casts = [
        'date' => 'date',
        'meta' => 'array',
        'is_custom' => 'bool',
        'is_locked' => 'bool',
    ];

    public function booking() { return $this->belongsTo(Booking::class); }

    public function sourceTourItem()
    {
        return $this->belongsTo(ItineraryItem::class, 'tour_itinerary_item_id');
    }

    public function assignments()
    {
        return $this->hasMany(\App\Models\BookingItineraryItemAssignment::class);
    }
}
