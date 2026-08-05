<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamLevelRange extends Model
{
    protected $table = 'exam_level_ranges';

    protected $fillable = ['level', 'min_score', 'max_score', 'status'];

    public static function recommendLevel(float $percentage): ?string
    {
        $range = self::where('status', 1)
            ->where('min_score', '<=', $percentage)
            ->where('max_score', '>=', $percentage)
            ->first();

        return $range?->level;
    }
}
