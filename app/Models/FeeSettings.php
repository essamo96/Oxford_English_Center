<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeSettings extends Model
{
    use HasFactory;

    protected $table = 'fee_settings';

    protected $fillable = [
        'program_id',
        'level_name',
        'amount',
        'currency',
        'type',
    ];

    /**
     * Relationship: belongs to a Program.
     */
    public function program()
    {
        return $this->belongsTo(Programs::class, 'program_id');
    }

    /**
     * Get fee for a specific program+level+type combination.
     */
    public static function getFee(int $programId, string $type, ?string $levelName = null): float
    {
        // Try specific level first
        if ($levelName) {
            $fee = static::where('program_id', $programId)
                ->where('type', $type)
                ->where('level_name', $levelName)
                ->first();
            if ($fee) return (float) $fee->amount;
        }

        // Fallback to general fee (null level_name)
        $fee = static::where('program_id', $programId)
            ->where('type', $type)
            ->whereNull('level_name')
            ->first();

        return $fee ? (float) $fee->amount : 0.0;
    }
}
