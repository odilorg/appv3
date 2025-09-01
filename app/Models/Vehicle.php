<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use SoftDeletes;
    protected $fillable = ['type','seats','plate','owner_driver_id','is_active','notes'];
    protected $casts = ['is_active' => 'boolean'];

    public function ownerDriver() {
        return $this->belongsTo(Driver::class, 'owner_driver_id');
    }
}
