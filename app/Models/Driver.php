<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Driver extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name',
        'email',
        'is_active',
        'notes',
        'address',
        'phone01',
        'phone02',
        'image',
        'license_number',
        'license_expires_at',
        'license_image',
    ];
    protected $casts = [
        'license_expires_at' => 'date',
        'is_active' => 'boolean',
    ];

    public function cars(): HasMany
    {
        return $this->hasMany(Car::class);
    }
}
