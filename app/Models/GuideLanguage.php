<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuideLanguage extends Pivot
{
    public $incrementing = true; // required when using an id PK
    protected $table = 'guide_language';
    protected $fillable = ['guide_id', 'language_id', 'level'];

    public function guide(): BelongsTo
    {
        return $this->belongsTo(Guide::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
