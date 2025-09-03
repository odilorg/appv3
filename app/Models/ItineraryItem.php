<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItineraryItem extends Model
{
    protected $fillable = [
        'tour_id','parent_id','type','sort_order','title','description',
        'default_start_time','duration_minutes','meta'
    ];

    protected $casts = ['meta' => 'array'];

    public function tour()         { return $this->belongsTo(Tour::class); }
    public function parent()       { return $this->belongsTo(self::class, 'parent_id'); }
    public function children()     { return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order'); }
}
