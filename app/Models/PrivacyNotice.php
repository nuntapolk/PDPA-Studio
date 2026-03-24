<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrivacyNotice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id', 'type', 'title', 'language', 'version',
        'content', 'effective_date', 'published_at', 'expires_at',
        'is_active', 'public_url', 'created_by', 'approved_by',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'expires_at'   => 'datetime',
        'is_active'    => 'boolean',
    ];

    // ── Relations ─────────────────────────────────────────────────────────
    public function organization() { return $this->belongsTo(Organization::class); }
    public function createdBy()    { return $this->belongsTo(User::class, 'created_by'); }
    public function approvedBy()   { return $this->belongsTo(User::class, 'approved_by'); }

    // ── Scopes ─────────────────────────────────────────────────────────────
    public function scopeActive($query)    { return $query->where('is_active', true)->whereNotNull('published_at'); }
    public function scopeDraft($query)     { return $query->whereNull('published_at'); }
    public function scopePublished($query) { return $query->whereNotNull('published_at')->where('is_active', true); }
    public function scopeExpiringSoon($query) {
        return $query->whereNotNull('expires_at')
                     ->where('expires_at', '>', now())
                     ->where('expires_at', '<=', now()->addDays(30))
                     ->where('is_active', true);
    }

    // ── Status helpers ─────────────────────────────────────────────────────
    public function getStatus(): string
    {
        if (!$this->published_at) return 'draft';
        if ($this->expires_at && $this->expires_at->isPast()) return 'expired';
        if (!$this->is_active) return 'inactive';
        return 'published';
    }

    public function getStatusLabel(): string
    {
        return match($this->getStatus()) {
            'draft'     => 'ร่าง',
            'published' => 'เผยแพร่แล้ว',
            'inactive'  => 'ปิดใช้งาน',
            'expired'   => 'หมดอายุ',
            default     => $this->getStatus(),
        };
    }

    public function getStatusBadge(): string
    {
        return match($this->getStatus()) {
            'published' => 'badge-green',
            'draft'     => 'badge-gray',
            'inactive'  => 'badge-yellow',
            'expired'   => 'badge-red',
            default     => 'badge-gray',
        };
    }

    // ── Type helpers ───────────────────────────────────────────────────────
    public static function typeLabel(string $type): string
    {
        return match($type) {
            'privacy_policy'     => 'นโยบายความเป็นส่วนตัว',
            'cookie_policy'      => 'นโยบาย Cookie',
            'employee_notice'    => 'ประกาศพนักงาน',
            'cctv_notice'        => 'ประกาศกล้องวงจรปิด',
            'marketing_notice'   => 'ประกาศการตลาด',
            'third_party_notice' => 'ประกาศบุคคลที่สาม',
            default              => $type,
        };
    }

    public function getTypeLabel(): string { return self::typeLabel($this->type); }

    public static function typeColor(string $type): string
    {
        return match($type) {
            'privacy_policy'     => '#15572e',
            'cookie_policy'      => '#0369a1',
            'employee_notice'    => '#7c3aed',
            'cctv_notice'        => '#b45309',
            'marketing_notice'   => '#db2777',
            'third_party_notice' => '#475569',
            default              => '#64748b',
        };
    }

    public static function typeBg(string $type): string
    {
        return match($type) {
            'privacy_policy'     => '#e8f0eb',
            'cookie_policy'      => '#e0f2fe',
            'employee_notice'    => '#ede9fe',
            'cctv_notice'        => '#fef3c7',
            'marketing_notice'   => '#fce7f3',
            'third_party_notice' => '#f1f5f9',
            default              => '#f8fafc',
        };
    }

    public static function typeIcon(string $type): string
    {
        return match($type) {
            'privacy_policy'     => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
            'cookie_policy'      => 'M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
            'employee_notice'    => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0',
            'cctv_notice'        => 'M15 10l4.553-2.069A1 1 0 0121 8.82v6.36a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z',
            'marketing_notice'   => 'M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z',
            'third_party_notice' => 'M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1',
            default              => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z',
        };
    }
}
