<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DpoTask extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id', 'title', 'description', 'category',
        'priority', 'status', 'due_date', 'assigned_to',
        'created_by', 'notes', 'completed_at',
    ];

    protected $casts = [
        'due_date'     => 'date',
        'completed_at' => 'datetime',
    ];

    // ── Relations ─────────────────────────────────────────────────────────
    public function organization() { return $this->belongsTo(Organization::class); }
    public function assignedTo()   { return $this->belongsTo(User::class, 'assigned_to'); }
    public function createdBy()    { return $this->belongsTo(User::class, 'created_by'); }

    // ── Scopes ─────────────────────────────────────────────────────────────
    public function scopePending($q)    { return $q->where('status', 'pending'); }
    public function scopeInProgress($q) { return $q->where('status', 'in_progress'); }
    public function scopeCompleted($q)  { return $q->where('status', 'completed'); }
    public function scopeOverdue($q)    {
        return $q->whereNotNull('due_date')
                 ->where('due_date', '<', now()->toDateString())
                 ->whereNotIn('status', ['completed', 'cancelled']);
    }
    public function scopeUrgent($q) { return $q->where('priority', 'urgent'); }
    public function scopeDueThisWeek($q) {
        return $q->whereNotNull('due_date')
                 ->whereBetween('due_date', [now()->toDateString(), now()->addDays(7)->toDateString()])
                 ->whereNotIn('status', ['completed', 'cancelled']);
    }

    // ── Helpers ────────────────────────────────────────────────────────────
    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast()
            && !in_array($this->status, ['completed', 'cancelled']);
    }

    public function getDaysUntilDue(): ?int
    {
        return $this->due_date ? now()->diffInDays($this->due_date, false) : null;
    }

    // ── Labels ─────────────────────────────────────────────────────────────
    public static function categoryLabel(string $cat): string
    {
        return match($cat) {
            'compliance_review' => 'ทบทวนความสอดคล้อง',
            'policy_update'     => 'อัปเดตนโยบาย',
            'training'          => 'จัดอบรม',
            'audit'             => 'ตรวจสอบ (Audit)',
            'vendor_review'     => 'ทบทวน Vendor',
            'incident_response' => 'ตอบสนองเหตุการณ์',
            'reporting'         => 'รายงาน',
            'other'             => 'อื่นๆ',
            default             => $cat,
        };
    }

    public static function categoryColor(string $cat): string
    {
        return match($cat) {
            'compliance_review' => '#15572e',
            'policy_update'     => '#0369a1',
            'training'          => '#7c3aed',
            'audit'             => '#b45309',
            'vendor_review'     => '#0891b2',
            'incident_response' => '#c0272d',
            'reporting'         => '#475569',
            default             => '#64748b',
        };
    }

    public static function priorityLabel(string $p): string
    {
        return match($p) {
            'low'    => 'ต่ำ',
            'medium' => 'ปานกลาง',
            'high'   => 'สูง',
            'urgent' => 'เร่งด่วน',
            default  => $p,
        };
    }

    public static function priorityBadge(string $p): string
    {
        return match($p) {
            'urgent' => 'badge-red',
            'high'   => 'badge-yellow',
            'medium' => 'badge-blue',
            'low'    => 'badge-gray',
            default  => 'badge-gray',
        };
    }

    public static function statusLabel(string $s): string
    {
        return match($s) {
            'pending'     => 'รอดำเนินการ',
            'in_progress' => 'กำลังดำเนินการ',
            'completed'   => 'เสร็จสิ้น',
            'cancelled'   => 'ยกเลิก',
            default       => $s,
        };
    }

    public static function statusBadge(string $s): string
    {
        return match($s) {
            'pending'     => 'badge-yellow',
            'in_progress' => 'badge-blue',
            'completed'   => 'badge-green',
            'cancelled'   => 'badge-gray',
            default       => 'badge-gray',
        };
    }
}
