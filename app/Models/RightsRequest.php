<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RightsRequest extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id', 'data_subject_id', 'ticket_number',
        'type', 'status',
        'requester_name', 'requester_email', 'requester_phone', 'requester_id_number',
        'description', 'data_scope',
        'assigned_to', 'due_date', 'response_note', 'response_file',
        'rejection_reason', 'submitted_at', 'acknowledged_at', 'completed_at',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'submitted_at' => 'datetime',
        'acknowledged_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function organization() { return $this->belongsTo(Organization::class); }
    public function dataSubject() { return $this->belongsTo(DataSubject::class); }
    public function assignee() { return $this->belongsTo(User::class, 'assigned_to'); }
    public function notes() { return $this->hasMany(RightsRequestNote::class); }

    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast()
            && !in_array($this->status, ['completed', 'rejected', 'withdrawn']);
    }

    public function getDaysRemainingAttribute(): ?int
    {
        if (!$this->due_date) return null;
        return max(0, now()->diffInDays($this->due_date, false));
    }

    public function getTypeLabel(): string
    {
        return match($this->type) {
            'access' => 'ขอเข้าถึงข้อมูล',
            'rectification' => 'ขอแก้ไขข้อมูล',
            'erasure' => 'ขอลบข้อมูล',
            'restriction' => 'ขอระงับการใช้',
            'portability' => 'ขอรับข้อมูล',
            'objection' => 'คัดค้านการประมวลผล',
            default => $this->type,
        };
    }

    public function getStatusLabel(): string
    {
        return match($this->status) {
            'pending' => 'รอดำเนินการ',
            'in_review' => 'กำลังตรวจสอบ',
            'awaiting_info' => 'รอข้อมูลเพิ่มเติม',
            'approved' => 'อนุมัติ',
            'completed' => 'เสร็จสิ้น',
            'rejected' => 'ปฏิเสธ',
            'withdrawn' => 'ถอนคำร้อง',
            default => $this->status,
        };
    }

    // Scopes
    public function scopeOverdue($query)
    {
        return $query->whereNotIn('status', ['completed', 'rejected', 'withdrawn'])
            ->where('due_date', '<', now());
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['pending', 'in_review', 'awaiting_info']);
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (!$model->ticket_number) {
                $count = static::whereYear('created_at', now()->year)->count() + 1;
                $model->ticket_number = 'RR-' . now()->year . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);
            }
            if (!$model->submitted_at) {
                $model->submitted_at = now();
            }
            if (!$model->due_date) {
                $model->due_date = now()->addDays(30);
            }
        });
    }
}
