<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class TransportRate extends Model
{
     protected $fillable = [
        'rate_item_id',
        'transport_type_id',
        'unit',
        'amount',
        'currency',
        'valid_from',
        'valid_to',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'valid_from' => 'date',
        'valid_to'   => 'date',
        'is_active'  => 'boolean',
        'amount'     => 'decimal:2',
    ];

    public function rateItem()
    {
        return $this->belongsTo(RateItem::class);
    }

    public function transportType()
    {
        return $this->belongsTo(TransportType::class);
    }

    /** Rates that are active and valid today (or open-ended). */
    public function scopeCurrent(Builder $q): Builder
    {
        $today = Carbon::today();
        return $q->where('is_active', true)
            ->where(function ($w) use ($today) {
                $w->whereNull('valid_from')->orWhere('valid_from', '<=', $today);
            })
            ->where(function ($w) use ($today) {
                $w->whereNull('valid_to')->orWhere('valid_to', '>=', $today);
            });
    }
}
