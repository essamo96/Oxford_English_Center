<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceSetting extends Model
{
    protected $table = 'attendance_settings';

    protected $fillable = [
        'allowed_ips', 'grace_minutes', 'enforce_ip', 'enforce_time',
    ];

    protected $casts = [
        'enforce_ip'   => 'boolean',
        'enforce_time' => 'boolean',
        'grace_minutes' => 'integer',
    ];

    /** Always work with a single settings row. */
    public static function current(): self
    {
        return static::query()->first() ?: static::create([
            'allowed_ips' => null, 'grace_minutes' => 15, 'enforce_ip' => false, 'enforce_time' => true,
        ]);
    }

    /** Parsed list of allowed IPs / CIDR ranges. */
    public function allowedIpList(): array
    {
        return collect(preg_split('/[\s,;]+/', (string) $this->allowed_ips))
            ->map(fn ($v) => trim($v))
            ->filter()
            ->values()
            ->all();
    }

    /**
     * Is the given client IP allowed? Supports exact IPs and CIDR (e.g. 192.168.1.0/24).
     * When enforcement is off, or no IPs are configured, it always passes.
     */
    public function ipAllowed(?string $ip): bool
    {
        if (!$this->enforce_ip) return true;
        $list = $this->allowedIpList();
        if (empty($list)) return true; // nothing configured → don't lock anyone out
        if (!$ip) return false;

        foreach ($list as $allowed) {
            if (str_contains($allowed, '/')) {
                if ($this->ipInCidr($ip, $allowed)) return true;
            } elseif ($ip === $allowed) {
                return true;
            }
        }
        return false;
    }

    private function ipInCidr(string $ip, string $cidr): bool
    {
        [$subnet, $bits] = array_pad(explode('/', $cidr), 2, null);
        $ipLong = ip2long($ip);
        $subnetLong = ip2long($subnet);
        if ($ipLong === false || $subnetLong === false || $bits === null) return false;
        $bits = (int) $bits;
        if ($bits < 0 || $bits > 32) return false;
        $mask = $bits === 0 ? 0 : (-1 << (32 - $bits));
        return ($ipLong & $mask) === ($subnetLong & $mask);
    }
}
