<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeType extends Model
{
    use HasFactory;

    protected $table = 'fee_types';

    protected $fillable = [
        'slug',
        'name_ar',
        'name_en',
        'icon',
        'class',
    ];
}
