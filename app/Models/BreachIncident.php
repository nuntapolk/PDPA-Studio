<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BreachIncident extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id', 'incident_number', 'title', 'description',
        'breach_type', 'severity', 'status',
        'discovered_at', 'occurred_at',
        'affected_count', 'data_types_affected', 'includes_sensitive_data', 'impact_assessment',
        'requires_pdpc_notification', 'pdpc_notification_deadline', 'pdpc_notified_at', 'pdpc_reference_number',
        'requires_subject_notification', 'subjects_notified_at',
        'containment_actions', 'root_cause', 'corrective_actions', 'preventive_measures',
        'reported_by', 'assigned_to', 'resolved_at',
    ];

    protected $casts = [
        'discovered_at' => 'datetime',
        'occurred_at' => 'datetime',
        'pdpc_notification_deadline' => 'datetime',
        'pdpc_notified_at' => 'datetime',
        'subjects_notified_at' => 'datetime',
        'resolved_at' => 'datetime',
        'data_types_affected' => 'array',
        'includes_sensitive_data' => 'boolean',
        'requires_pdpc_notification' => 'boolean',
        'requires_subject_notification' => 'boolean',
    ];

    public function organization() { return $this->belongsTo(Organization::class); }
    public function reporter() { return $this->belongsTo(User::class, 'reported_by'); }
    public function assignee() { return $this->belongsTo(User::class, 'assigned_to'); }
    public function timeline() { return $this->hasMany(BreachTimeline::class, 'breach_incident_id')->orderBy('created_at', 'desc'); }

    public function getHoursUntilDeadlineAttribute(): ?int
    {
        if (!$this->pdpc_notification_deadline) return null;
        return max(0, (int) now()->diffInHours($this->pdpc_notification_deadline, false));
    }

    public function isPdpcDeadlinePassed(): bool
    {
        return $this->pdpc_notification_deadline
            && $this->pdpc_notification_deadline->isPast()
            && !$this->pdpc_notified_at;
    }

    public function getSeverityLabel(): string
    {
        return match($this->severity) {
            'low' => 'ต่ำ',
            'medium' => 'ปานกลาง',
            'high' => 'สูง',
            'critical' => 'วิกฤต',
            default => $this->severity,
        };
    }

    public function getSeverityColor(): string
    {
        return match($this->severity) {
            'low' => 'green',
            'medium' => 'yellow',
            'high' => 'orange',
            'critical' => 'red',
            default => 'gray',
        };
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (!$model->incident_number) {
                $count = static::whereYear('created_at', now()->year)->count() + 1;
                $model->incident_number = 'BR-' . now()->year . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);
            }
            // ตั้ง deadline 72 ชั่วโมงอัตโนมัติ
            if (!$model->pdpc_notification_deadline && $model->discovered_at) {
                $model->pdpc_notification_deadline = $model->discovered_at->addHours(72);
            }
        });
    }
}
