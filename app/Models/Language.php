<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Language extends Model
{
    protected $fillable = ['name', 'code'];

    public function guides(): BelongsToMany
    {
        return $this->belongsToMany(Guide::class, 'guide_language')
            ->withPivot(['level'])
            ->withTimestamps();
    }

    public function guideLanguages(): HasMany
    {
        return $this->hasMany(GuideLanguage::class);
    }
}
