<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Parents extends Model
{
    use SoftDeletes;

    protected $table = 'parents';

    protected $fillable = [
        'name',
        'phone',
        'email',
        'relationship',
    ];

    public function students()
    {
        return $this->hasMany(Students::class, 'parent_id');
    }
}
