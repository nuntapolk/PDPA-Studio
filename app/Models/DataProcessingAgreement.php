<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DataProcessingAgreement extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'external_party_id','dpa_number','title','type',
        'our_role','their_role','signatory_our','signatory_their',
        'status','signed_at','effective_at','expires_at','auto_renew','termination_notice_days',
        'data_categories','processing_purposes','sub_processors_allowed',
        'security_requirements','audit_rights','breach_notification_hours',
        'file_path','file_hash','version','supersedes_id','notes','created_by',
    ];

    protected $casts = [
        'data_categories'       => 'array',
        'processing_purposes'   => 'array',
        'sub_processors_allowed'=> 'boolean',
        'audit_rights'          => 'boolean',
        'auto_renew'            => 'boolean',
        'signed_at'             => 'date',
        'effective_at'          => 'date',
        'expires_at'            => 'date',
    ];

    public function externalParty() { return $this->belongsTo(ExternalParty::class); }
    public function supersedes() { return $this->belongsTo(self::class, 'supersedes_id'); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }

    public static function typeLabel(string $t): string {
        return match($t) {
            'dpa'  => 'DPA', 'jca' => 'JCA', 'addendum' => 'Addendum',
            'nda'  => 'NDA', 'data_sharing_agreement' => 'Data Sharing Agreement',
            default => $t,
        };
    }
    public static function statusColor(string $s): string {
        return match($s) {
            'active'            => '#15572e',
            'draft'             => '#64748b',
            'pending_signature' => '#d97706',
            'expired'           => '#c0272d',
            'terminated'        => '#7f1d1d',
            'superseded'        => '#94a3b8',
            default             => '#64748b',
        };
    }
    public static function statusBg(string $s): string {
        return match($s) {
            'active'            => '#dcfce7',
            'draft'             => '#f1f5f9',
            'pending_signature' => '#fef3c7',
            'expired'           => '#fee2e2',
            'terminated'        => '#fde8d8',
            'superseded'        => '#f8fafc',
            default             => '#f1f5f9',
        };
    }

    public function isExpiringSoon(): bool {
        return $this->expires_at && $this->expires_at->diffInDays(now()) <= 60 && !$this->expires_at->isPast();
    }
    public function getDaysUntilExpiry(): ?int {
        return $this->expires_at ? (int) now()->diffInDays($this->expires_at, false) : null;
    }
}
