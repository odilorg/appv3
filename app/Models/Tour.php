<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tour extends Model
{
     protected $fillable = ['title','duration_days','short_description','long_description','is_active'];

    public function itineraryItems()
    {
        return $this->hasMany(ItineraryItem::class)->orderBy('sort_order');
    }

    public function topLevelItems()
    {
        return $this->itineraryItems()->whereNull('parent_id');
    }
}
