<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organization extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name','slug','tax_id','industry','address','website','logo_path',
        'primary_pdpa_role','pdpa_registration_no','pdpa_certified_at',
        'dpo_name','dpo_email','dpo_phone','dpo_appointed_at','dpo_is_external',
        'legal_rep_name','privacy_email','privacy_phone',
        'plan','status','max_users','trial_ends_at','settings',
    ];

    protected $casts = [
        'trial_ends_at'    => 'datetime',
        'pdpa_certified_at'=> 'date',
        'dpo_appointed_at' => 'date',
        'dpo_is_external'  => 'boolean',
        'settings'         => 'array',
    ];

    // ── Relationships ────────────────────────────────────────────────────────
    public function users()           { return $this->hasMany(User::class); }
    public function dataSubjects()    { return $this->hasMany(DataSubject::class); }
    public function consentTemplates(){ return $this->hasMany(ConsentTemplate::class); }
    public function consents()        { return $this->hasMany(Consent::class); }
    public function rightsRequests()  { return $this->hasMany(RightsRequest::class); }
    public function ropaRecords()     { return $this->hasMany(RopaRecord::class); }
    public function breachIncidents() { return $this->hasMany(BreachIncident::class); }
    public function privacyNotices()  { return $this->hasMany(PrivacyNotice::class); }
    public function dpoTasks()        { return $this->hasMany(DpoTask::class); }
    public function vendors()         { return $this->hasMany(Vendor::class); }
    public function assessments()     { return $this->hasMany(Assessment::class); }
    public function trainingCourses() { return $this->hasMany(TrainingCourse::class); }
    public function apiKeys()         { return $this->hasMany(ApiKey::class); }
    public function webhooks()        { return $this->hasMany(Webhook::class); }
    public function auditLogs()       { return $this->hasMany(AuditLog::class); }
    public function externalParties() { return $this->hasMany(ExternalParty::class, 'created_by'); }

    // ── Scopes ───────────────────────────────────────────────────────────────
    public function scopeActive($query) { return $query->where('status','active'); }

    // ── PDPA Role Helpers ────────────────────────────────────────────────────
    public function isController(): bool { return in_array($this->primary_pdpa_role, ['controller','both']); }
    public function isProcessor(): bool  { return in_array($this->primary_pdpa_role, ['processor','both']); }
    public function pdpaRoleLabel(): string {
        return match($this->primary_pdpa_role) {
            'controller' => 'Data Controller',
            'processor'  => 'Data Processor',
            'both'       => 'DC & DP',
            default      => 'ไม่ระบุ',
        };
    }

    // ── Compliance Score (updated for external parties) ──────────────────────
    public function getComplianceScore(): int
    {
        $scores = [];
        $scores[] = $this->consentTemplates()->where('is_active',true)->exists() ? 20 : 0;
        $scores[] = $this->ropaRecords()->where('status','active')->exists() ? 20 : 0;
        $scores[] = $this->privacyNotices()->where('is_active',true)->exists() ? 15 : 0;
        $scores[] = ExternalParty::whereHas('dpas', fn($q)=>$q->where('status','active'))->exists() ? 15 : 0;
        $scores[] = $this->trainingCourses()->where('is_active',true)->exists() ? 15 : 0;
        $scores[] = $this->assessments()->where('status','completed')->exists() ? 15 : 0;
        return array_sum($scores);
    }
}
