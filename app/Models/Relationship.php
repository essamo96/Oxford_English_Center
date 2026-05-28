<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Relationship extends Model
{
    protected $table = 'relationships';

    protected $fillable = [
        'name_ar', 'name_en', 'slug', 'is_active', 'is_other', 'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_other'  => 'boolean',
    ];

    public function scopeActive($q)
    {
        return $q->where('is_active', true);
    }
}
