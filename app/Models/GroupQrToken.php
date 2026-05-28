<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class GroupQrToken extends Model
{
    protected $table = 'group_qr_tokens';

    protected $fillable = [
        'group_id',
        'token',
        'expires_at',
        'created_by_type',
        'created_by_id',
        'max_uses',
        'uses_count',
        'is_active',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_active'  => 'boolean',
    ];

    public function group()
    {
        return $this->belongsTo(Groups::class, 'group_id');
    }

    /**
     * Is this token still valid (active, not expired, not over-used)?
     */
    public function isUsable(): bool
    {
        if (!$this->is_active) return false;
        if ($this->expires_at && Carbon::now()->greaterThan($this->expires_at)) return false;
        if ($this->max_uses !== null && $this->uses_count >= $this->max_uses) return false;
        return true;
    }

    public function reasonInvalid(): ?string
    {
        if (!$this->is_active) return 'هذا الـ QR Code معطّل من قبل المسؤول.';
        if ($this->expires_at && Carbon::now()->greaterThan($this->expires_at)) return 'انتهت صلاحية هذا الـ QR Code.';
        if ($this->max_uses !== null && $this->uses_count >= $this->max_uses) return 'تم استنفاد عدد مرات استخدام هذا الـ QR Code.';
        return null;
    }
}
