<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class OperationLog extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'organization_id','user_id','user_name',
        'method','url','route_name','route_action',
        'status_code','duration_ms','memory_mb',
        'request_size','response_size',
        'ip_address','session_id','user_agent','referer','created_at',
    ];
    protected $casts = ['created_at' => 'datetime'];
    public function organization() { return $this->belongsTo(Organization::class); }
    public function user() { return $this->belongsTo(User::class); }

    public function getStatusBadgeAttribute(): array
    {
        return match(true) {
            $this->status_code < 300 => ['bg'=>'#dcfce7','color'=>'#15572e','label'=>'2xx'],
            $this->status_code < 400 => ['bg'=>'#dbeafe','color'=>'#1d4ed8','label'=>'3xx'],
            $this->status_code < 500 => ['bg'=>'#fef3c7','color'=>'#92400e','label'=>'4xx'],
            default                  => ['bg'=>'#fee2e2','color'=>'#c0272d','label'=>'5xx'],
        };
    }

    public function getDurationBadgeAttribute(): string
    {
        if ($this->duration_ms < 200) return 'fast';
        if ($this->duration_ms < 1000) return 'normal';
        if ($this->duration_ms < 3000) return 'slow';
        return 'critical';
    }
}
