<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'organization_id', 'user_id', 'user_name',
        'action', 'module', 'entity_type', 'entity_id', 'entity_name',
        'old_values', 'new_values', 'ip_address', 'user_agent', 'url', 'method',
        'created_at',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    public function organization() { return $this->belongsTo(Organization::class); }
    public function user() { return $this->belongsTo(User::class); }

    public static function record(string $action, string $module, $entity = null, array $oldValues = [], array $newValues = []): void
    {
        $user = auth()->user();
        static::create([
            'organization_id' => $user?->organization_id,
            'user_id' => $user?->id,
            'user_name' => $user?->name,
            'action' => $action,
            'module' => $module,
            'entity_type' => $entity ? get_class($entity) : null,
            'entity_id' => $entity?->id,
            'entity_name' => $entity?->name ?? $entity?->title ?? null,
            'old_values' => $oldValues ?: null,
            'new_values' => $newValues ?: null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'url' => request()->fullUrl(),
            'method' => request()->method(),
            'created_at' => now(),
        ]);
    }
}
