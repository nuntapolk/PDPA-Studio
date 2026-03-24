<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SystemErrorLog extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'organization_id','user_id','level','channel','message',
        'exception_class','file','line','stack_trace','context',
        'request_url','request_method','ip_address',
        'environment','app_version',
        'is_resolved','resolved_at','resolved_by','resolution_note',
        'occurrence_count','last_occurred_at','created_at',
    ];
    protected $casts = [
        'context'          => 'array',
        'is_resolved'      => 'boolean',
        'resolved_at'      => 'datetime',
        'last_occurred_at' => 'datetime',
        'created_at'       => 'datetime',
    ];
    public function organization() { return $this->belongsTo(Organization::class); }
    public function user() { return $this->belongsTo(User::class); }

    public static function levelColor(string $l): string
    {
        return match($l) {
            'debug'     => '#94a3b8',
            'info'      => '#3b82f6',
            'notice'    => '#06b6d4',
            'warning'   => '#f59e0b',
            'error'     => '#ef4444',
            'critical'  => '#dc2626',
            'alert'     => '#b91c1c',
            'emergency' => '#7f1d1d',
            default     => '#64748b',
        };
    }
    public static function levelBg(string $l): string
    {
        return match($l) {
            'debug','info','notice' => '#f1f5f9',
            'warning'               => '#fef3c7',
            'error','critical'      => '#fee2e2',
            'alert','emergency'     => '#fde8d8',
            default                 => '#f1f5f9',
        };
    }

    public static function capture(\Throwable $e, array $context = []): void
    {
        $user = auth()->user();
        static::create([
            'organization_id' => $user?->organization_id,
            'user_id'         => $user?->id,
            'level'           => 'error',
            'channel'         => 'app',
            'message'         => $e->getMessage(),
            'exception_class' => get_class($e),
            'file'            => str_replace(base_path().'/', '', $e->getFile()),
            'line'            => $e->getLine(),
            'stack_trace'     => $e->getTraceAsString(),
            'context'         => $context ?: null,
            'request_url'     => request()->fullUrl(),
            'request_method'  => request()->method(),
            'ip_address'      => request()->ip(),
            'environment'     => app()->environment(),
            'app_version'     => config('app.version', '1.0'),
            'created_at'      => now(),
            'last_occurred_at'=> now(),
        ]);
    }
}
