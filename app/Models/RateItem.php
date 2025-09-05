<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RateItem extends Model
{
    protected $fillable = [
        'mode',
        'code',
        'name',
        'default_unit',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
{
    static::saving(function (RateItem $item) {
        if (blank($item->code) && filled($item->name)) {
            $base = \Illuminate\Support\Str::upper(
                str_replace('-', '_', \Illuminate\Support\Str::slug($item->name, '_'))
            );
            $code = $base ?: 'ITEM';
            $i = 1;
            while (static::where('code', $code)->when($item->exists, fn($q)=>$q->where('id','!=',$item->id))->exists()) {
                $code = $base . '_' . ++$i;
            }
            $item->code = $code;
        }
    });
}

    // Relationships
    public function transportRates()
    {
        return $this->hasMany(\App\Models\TransportRate::class);
    }

    // Common scopes
    public function scopeActive($q) { return $q->where('is_active', true); }
    public function scopeMode($q, string $mode) { return $q->where('mode', $mode); }
}
