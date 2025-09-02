<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * @property \Illuminate\Support\Carbon $start_date
 * @property \Illuminate\Support\Carbon $end_date
 */
class Booking extends Model
{
   protected $fillable = [
        'customer_id','tour_id','start_date','end_date','pax_total','status','currency','total_price','notes'
    ];

    protected $casts = ['start_date'=>'date','end_date'=>'date'];

    public function tour()     { return $this->belongsTo(Tour::class); }
    public function customer() { return $this->belongsTo(Customer::class); }
    public function items()    { return $this->hasMany(BookingItineraryItem::class)->orderBy('sort_order'); }

    public function refreshDatesFromTrip(): void
{
    if ($this->tour && $this->start_date) {
        $days = max(1, (int) $this->tour->duration_days);
        $this->end_date = (clone $this->start_date)->addDays($days - 1);
    }
}
}
