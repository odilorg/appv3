<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Guide extends Model
{
   use SoftDeletes;
    protected $fillable = ['name','base_city','phone','email','languages','is_active','notes'];
    protected $casts = ['languages' => 'array', 'is_active' => 'boolean'];
}
