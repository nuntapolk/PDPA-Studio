<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConsentTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id', 'name', 'slug', 'version', 'purpose', 'description',
        'legal_basis', 'retention_days', 'data_categories',
        'is_sensitive', 'requires_explicit_consent', 'withdrawal_info',
        'is_active', 'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_sensitive' => 'boolean',
        'requires_explicit_consent' => 'boolean',
    ];

    public function organization() { return $this->belongsTo(Organization::class); }
    public function consents() { return $this->hasMany(Consent::class, 'template_id'); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }

    public function getLegalBasisLabel(): string
    {
        return match($this->legal_basis) {
            'consent' => 'ความยินยอม (มาตรา 19)',
            'contract' => 'สัญญา (มาตรา 24(3))',
            'legal_obligation' => 'หน้าที่ตามกฎหมาย (มาตรา 24(1))',
            'legitimate_interest' => 'ประโยชน์อันชอบธรรม (มาตรา 24(5))',
            'public_interest' => 'ประโยชน์สาธารณะ (มาตรา 24(4))',
            'vital_interest' => 'ประโยชน์ต่อชีวิต (มาตรา 24(2))',
            default => $this->legal_basis,
        };
    }

    public function getGrantedCountAttribute(): int
    {
        return $this->consents()->where('granted', true)->count();
    }

    public function getWithdrawnCountAttribute(): int
    {
        return $this->consents()->whereNotNull('withdrawn_at')->count();
    }
}
