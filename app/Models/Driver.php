<?php

namespace App\Models;

use App\Enums\CustomerType; // individual | company
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Driver extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'type',               // NEW
        'company_id',         // NEW (nullable)
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
        'type'                => CustomerType::class, // enum cast
        'license_expires_at'  => 'date',
        'is_active'           => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function cars(): HasMany
    {
        return $this->hasMany(Car::class);
    }
}
