<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExternalParty extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code','name','name_en','type','tax_id','registration_no','country','industry',
        'relationship_type','relationship_started_at','relationship_ended_at',
        'services_description','data_types_shared','processing_purposes','systems_involved',
        'risk_level','risk_notes',
        'is_cross_border','transfer_mechanism','transfer_countries','tia_required','tia_completed_at',
        'address','website','email','phone',
        'contact_name','contact_email','contact_phone',
        'dpo_name','dpo_email','dpo_phone',
        'status','review_frequency_months','next_review_date','notes','created_by',
    ];

    protected $casts = [
        'data_types_shared'      => 'array',
        'processing_purposes'    => 'array',
        'systems_involved'       => 'array',
        'transfer_countries'     => 'array',
        'is_cross_border'        => 'boolean',
        'tia_required'           => 'boolean',
        'relationship_started_at'=> 'date',
        'relationship_ended_at'  => 'date',
        'tia_completed_at'       => 'date',
        'next_review_date'       => 'date',
    ];

    // ── Relationships ────────────────────────────────────────────────────────
    public function dpas() { return $this->hasMany(DataProcessingAgreement::class); }
    public function activeDpa() { return $this->hasOne(DataProcessingAgreement::class)->where('status','active')->latest(); }
    public function assessments() { return $this->hasMany(ExternalPartyAssessment::class)->latest(); }
    public function latestAssessment() { return $this->hasOne(ExternalPartyAssessment::class)->latest(); }
    public function ropaRecords() { return $this->belongsToMany(RopaRecord::class, 'ropa_external_parties')->withPivot('party_role','data_categories','purpose'); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }

    // ── Role Labels ──────────────────────────────────────────────────────────
    public static function relationshipLabel(string $t): string {
        return match($t) {
            'data_processor'       => 'Data Processor',
            'data_controller'      => 'Data Controller',
            'joint_controller'     => 'Joint Controller',
            'sub_processor'        => 'Sub-Processor',
            'recipient'            => 'Recipient',
            'third_party'          => 'Third Party',
            'supervisory_authority'=> 'หน่วยงานกำกับดูแล',
            default => $t,
        };
    }
    public static function relationshipColor(string $t): string {
        return match($t) {
            'data_processor'       => '#1d4ed8',
            'data_controller'      => '#15572e',
            'joint_controller'     => '#7c3aed',
            'sub_processor'        => '#0369a1',
            'recipient'            => '#d97706',
            'third_party'          => '#64748b',
            'supervisory_authority'=> '#6b7280',
            default => '#64748b',
        };
    }
    public static function relationshipBg(string $t): string {
        return match($t) {
            'data_processor'       => '#dbeafe',
            'data_controller'      => '#dcfce7',
            'joint_controller'     => '#ede9fe',
            'sub_processor'        => '#e0f2fe',
            'recipient'            => '#fef3c7',
            'third_party'          => '#f1f5f9',
            'supervisory_authority'=> '#f3f4f6',
            default => '#f1f5f9',
        };
    }
    public static function relationshipIcon(string $t): string {
        return match($t) {
            'data_processor'       => '⚙️',
            'data_controller'      => '🏢',
            'joint_controller'     => '🤝',
            'sub_processor'        => '🔗',
            'recipient'            => '📤',
            'third_party'          => '👥',
            'supervisory_authority'=> '🏛',
            default => '🔹',
        };
    }
    public static function riskColor(string $r): string {
        return match($r) {
            'low'      => '#15572e', 'medium' => '#d97706',
            'high'     => '#dc2626', 'critical'=> '#7f1d1d',
            default    => '#64748b',
        };
    }
    public static function riskBg(string $r): string {
        return match($r) {
            'low'    => '#dcfce7', 'medium' => '#fef3c7',
            'high'   => '#fee2e2', 'critical'=> '#fde8d8',
            default  => '#f1f5f9',
        };
    }

    // ── Computed ─────────────────────────────────────────────────────────────
    public function getDpaStatusAttribute(): string {
        $dpa = $this->activeDpa;
        if (!$dpa) return 'none';
        if ($dpa->expires_at && $dpa->expires_at->isPast()) return 'expired';
        if ($dpa->expires_at && $dpa->expires_at->diffInDays(now()) <= 60) return 'expiring';
        return 'active';
    }

    public function isOverdue(): bool {
        return $this->next_review_date && $this->next_review_date->isPast();
    }

    // ── Scopes ───────────────────────────────────────────────────────────────
    public function scopeActive($q) { return $q->where('status','active'); }
    public function scopeByRole($q, string $role) { return $q->where('relationship_type', $role); }
    public function scopeCrossBorder($q) { return $q->where('is_cross_border', true); }
    public function scopeNeedReview($q) { return $q->where('next_review_date', '<=', now()->addDays(30)); }
    public function scopeNoDpa($q) {
        return $q->whereDoesntHave('dpas', fn($d) => $d->where('status','active'));
    }
}
