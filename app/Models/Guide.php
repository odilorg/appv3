<?php

namespace App\Models;

use App\Enums\CustomerType; // reuse enum: individual|company
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Guide extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'type',           // NEW: individual|company
        'company_id',     // NEW: link to companies.id when type=company
        'is_marketing',
        'phone',
        'email',
        'address',
        'city',
        'image',
        'price_types',
    ];

    protected $casts = [
        'type'         => CustomerType::class, // enum cast
        'is_marketing' => 'boolean',
        'price_types'  => 'array',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    // many-to-many (Language master list)
    public function languages(): BelongsToMany
    {
        return $this->belongsToMany(Language::class, 'guide_language')
            ->withPivot(['level'])
            ->withTimestamps();
    }

    // hasMany pivot-model for Repeater UI
    public function guideLanguages(): HasMany
    {
        return $this->hasMany(GuideLanguage::class);
    }
}
