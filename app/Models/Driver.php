<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Driver extends Model
{
    use SoftDeletes;
    protected $fillable = ['name','base_city','phone','email','is_active','notes'];
    protected $casts = ['is_active' => 'boolean'];

    public function vehicles() {
        return $this->hasMany(Vehicle::class, 'owner_driver_id');
    }
}
