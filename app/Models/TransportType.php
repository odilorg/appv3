<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransportType extends Model
{
     protected $fillable = [
        'mode','code','name','capacity','description','is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

  //  (optional) if you later link vehicles:
    public function cars()
    {
        return $this->hasMany(Car::class);
    }
}
