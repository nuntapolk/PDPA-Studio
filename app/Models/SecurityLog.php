<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SecurityLog extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'organization_id','user_id','user_name',
        'event_type','severity','description',
        'ip_address','user_agent','metadata',
        'is_resolved','resolved_at','resolved_by','created_at',
    ];
    protected $casts = [
        'metadata'    => 'array',
        'is_resolved' => 'boolean',
        'resolved_at' => 'datetime',
        'created_at'  => 'datetime',
    ];
    public function organization() { return $this->belongsTo(Organization::class); }
    public function user() { return $this->belongsTo(User::class); }

    public static function severityColor(string $s): string
    {
        return match($s) {
            'low'      => '#64748b',
            'medium'   => '#d97706',
            'high'     => '#dc2626',
            'critical' => '#7c2d12',
            default    => '#64748b',
        };
    }
    public static function severityBg(string $s): string
    {
        return match($s) {
            'low'      => '#f1f5f9',
            'medium'   => '#fef3c7',
            'high'     => '#fee2e2',
            'critical' => '#fde8d8',
            default    => '#f1f5f9',
        };
    }

    public static function eventIcon(string $e): string
    {
        return match($e) {
            'login_failed'          => '🔐',
            'login_success'         => '✅',
            'logout'                => '🚪',
            'account_locked'        => '🔒',
            'account_unlocked'      => '🔓',
            'mfa_enabled'           => '🛡',
            'mfa_disabled'          => '⚠️',
            'mfa_verified'          => '✔️',
            'mfa_failed'            => '❌',
            'password_changed'      => '🔑',
            'password_reset'        => '🔄',
            'permission_denied'     => '⛔',
            'session_expired'       => '⏰',
            'token_created'         => '🎫',
            'token_revoked'         => '🗑',
            'suspicious_ip'         => '🚨',
            'brute_force_detected'  => '💥',
            'data_export'           => '📤',
            default                 => '🔔',
        };
    }

    public static function record(
        string  $eventType,
        string  $severity    = 'medium',
        ?string $description = null,
        array   $metadata    = []
    ): void {
        $user = auth()->user();
        static::create([
            'organization_id' => $user?->organization_id,
            'user_id'         => $user?->id,
            'user_name'       => $user?->name ?? ($metadata['attempted_email'] ?? null),
            'event_type'      => $eventType,
            'severity'        => $severity,
            'description'     => $description,
            'ip_address'      => request()->ip(),
            'user_agent'      => request()->userAgent(),
            'metadata'        => $metadata ?: null,
            'created_at'      => now(),
        ]);
    }
}
