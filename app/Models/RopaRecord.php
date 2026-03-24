<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RopaRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id', 'process_name', 'process_code', 'department',
        'process_owner', 'role', 'purpose', 'legal_basis',
        'legitimate_interest_description', 'data_categories', 'data_subject_types',
        'has_sensitive_data', 'sensitive_data_categories',
        'recipients', 'third_party_transfer', 'cross_border_transfer',
        'cross_border_countries', 'cross_border_safeguards',
        'retention_period', 'retention_criteria', 'deletion_method',
        'security_measures', 'system_used',
        'status', 'last_reviewed_at', 'next_review_date',
        'reviewed_by', 'created_by',
    ];

    protected $casts = [
        'data_categories' => 'array',
        'data_subject_types' => 'array',
        'sensitive_data_categories' => 'array',
        'recipients' => 'array',
        'security_measures' => 'array',
        'has_sensitive_data' => 'boolean',
        'third_party_transfer' => 'boolean',
        'cross_border_transfer' => 'boolean',
        'last_reviewed_at' => 'date',
        'next_review_date' => 'date',
    ];

    public function organization() { return $this->belongsTo(Organization::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function reviewer() { return $this->belongsTo(User::class, 'reviewed_by'); }

    public function needsReview(): bool
    {
        return $this->next_review_date && $this->next_review_date->isPast();
    }

    public function getLegalBasisLabel(): string
    {
        return match($this->legal_basis) {
            'consent' => 'ความยินยอม',
            'contract' => 'สัญญา',
            'legal_obligation' => 'หน้าที่ตามกฎหมาย',
            'legitimate_interest' => 'ประโยชน์อันชอบธรรม',
            'public_interest' => 'ประโยชน์สาธารณะ',
            'vital_interest' => 'ประโยชน์ต่อชีวิต',
            default => $this->legal_basis,
        };
    }
}
