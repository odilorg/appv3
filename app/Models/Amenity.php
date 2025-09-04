<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Amenity extends Model
{
    protected $fillable = ['name','scope','icon'];

    public function hotels(): BelongsToMany
    {
        return $this->belongsToMany(Hotel::class, 'amenity_hotel');
    }

    public function rooms(): BelongsToMany
    {
        return $this->belongsToMany(Room::class, 'amenity_room');
    }
}
