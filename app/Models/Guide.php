<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Guide extends Model
{
   use SoftDeletes;
     protected $fillable = [
        'name',
     'is_marketing',
    'phone',
    'email',
    'address',
    'city',
    'image',
    'price_types',
    ];

    protected $casts = [
        'price_types' => 'array', // Cast price_types as an array
    ];

     // standard many-to-many (still useful for queries)
    public function languages(): BelongsToMany
    {
        return $this->belongsToMany(Language::class, 'guide_language')
            ->withPivot(['level'])
            ->withTimestamps();
    }

    // HasMany to the pivot model – used by Repeater
    public function guideLanguages(): HasMany
    {
        return $this->hasMany(GuideLanguage::class);
    }

}
