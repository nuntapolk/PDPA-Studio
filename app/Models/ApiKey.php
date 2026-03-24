<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ApiKey extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id', 'name', 'key_prefix', 'key_hash',
        'permissions', 'allowed_ips', 'last_used_at', 'expires_at', 'is_active', 'created_by',
    ];

    protected $casts = [
        'permissions' => 'array',
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function organization() { return $this->belongsTo(Organization::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }

    public static function generate(int $organizationId, string $name, array $permissions, int $userId): array
    {
        $rawKey = 'psk_' . Str::random(48);
        $prefix = substr($rawKey, 0, 8);

        $apiKey = static::create([
            'organization_id' => $organizationId,
            'name' => $name,
            'key_prefix' => $prefix,
            'key_hash' => bcrypt($rawKey),
            'permissions' => $permissions,
            'is_active' => true,
            'created_by' => $userId,
        ]);

        return ['api_key' => $apiKey, 'raw_key' => $rawKey];
    }

    public function verify(string $rawKey): bool
    {
        return password_verify($rawKey, $this->key_hash);
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function hasPermission(string $permission): bool
    {
        return in_array('*', $this->permissions ?? [])
            || in_array($permission, $this->permissions ?? []);
    }
}
