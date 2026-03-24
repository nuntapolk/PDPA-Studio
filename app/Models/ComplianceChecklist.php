<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplianceChecklist extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id', 'category', 'item', 'description',
        'reference', 'status', 'evidence_path', 'notes',
        'due_date', 'completed_at', 'responsible_user', 'sort_order',
    ];

    protected $casts = [
        'due_date'     => 'date',
        'completed_at' => 'date',
    ];

    // ── Relations ─────────────────────────────────────────────────────────
    public function organization()    { return $this->belongsTo(Organization::class); }
    public function responsibleUser() { return $this->belongsTo(User::class, 'responsible_user'); }

    // ── Scopes ─────────────────────────────────────────────────────────────
    public function scopeCompleted($q)  { return $q->where('status', 'completed'); }
    public function scopePending($q)    { return $q->whereIn('status', ['not_started', 'in_progress']); }
    public function scopeByCategory($q, string $cat) { return $q->where('category', $cat); }

    // ── Labels ─────────────────────────────────────────────────────────────
    public static function categoryLabel(string $cat): string
    {
        return match($cat) {
            'consent'  => 'ความยินยอม',
            'rights'   => 'สิทธิ์เจ้าของข้อมูล',
            'ropa'     => 'ROPA',
            'breach'   => 'Data Breach',
            'security' => 'มาตรการรักษาความปลอดภัย',
            'policy'   => 'นโยบายและประกาศ',
            'training' => 'การอบรม',
            'vendor'   => 'Vendor / ผู้ประมวลผล',
            default    => $cat,
        };
    }

    public static function statusLabel(string $s): string
    {
        return match($s) {
            'not_started' => 'ยังไม่เริ่ม',
            'in_progress' => 'กำลังดำเนินการ',
            'completed'   => 'เสร็จสิ้น',
            'na'          => 'ไม่เกี่ยวข้อง',
            default       => $s,
        };
    }

    public static function statusBadge(string $s): string
    {
        return match($s) {
            'completed'   => 'badge-green',
            'in_progress' => 'badge-blue',
            'not_started' => 'badge-gray',
            'na'          => 'badge-gray',
            default       => 'badge-gray',
        };
    }

    public function getCompletionPercent(string $orgId, string $category = null): float
    {
        $q = self::where('organization_id', $orgId)->where('status', '!=', 'na');
        if ($category) $q->where('category', $category);
        $total = $q->count();
        if (!$total) return 0;
        $done = (clone $q)->where('status', 'completed')->count();
        return round($done / $total * 100);
    }
}
