<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Room extends Model
{
    protected $fillable = [
        'hotel_id','name','code','layout','size_m2','max_adults','max_children',
        'base_price_usd','description','images','is_active',
    ];
     protected $casts = [
        'images' => 'array',
        'is_active' => 'boolean',
    ];

    public function hotel(): BelongsTo { return $this->belongsTo(Hotel::class); }

    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class, 'amenity_room')
            ->where('scope','room');
    }
}
