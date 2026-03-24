<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vendor extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id', 'name', 'tax_id', 'website', 'country',
        'contact_name', 'contact_email', 'contact_phone',
        'services_description', 'data_types_shared', 'role', 'risk_level',
        'is_cross_border', 'transfer_mechanism',
        'dpa_signed', 'dpa_signed_at', 'dpa_expires_at', 'dpa_file_path',
        'status', 'created_by',
    ];

    protected $casts = [
        'data_types_shared' => 'array',
        'is_cross_border' => 'boolean',
        'dpa_signed' => 'boolean',
        'dpa_signed_at' => 'date',
        'dpa_expires_at' => 'date',
    ];

    public function organization() { return $this->belongsTo(Organization::class); }
    public function assessments() { return $this->hasMany(VendorAssessment::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }

    public function isDpaExpired(): bool
    {
        return $this->dpa_signed && $this->dpa_expires_at && $this->dpa_expires_at->isPast();
    }

    public function isDpaExpiringSoon(int $days = 30): bool
    {
        return $this->dpa_signed
            && $this->dpa_expires_at
            && $this->dpa_expires_at->isFuture()
            && $this->dpa_expires_at->diffInDays(now()) <= $days;
    }

    public function getRiskColor(): string
    {
        return match($this->risk_level) {
            'low' => 'green',
            'medium' => 'yellow',
            'high' => 'red',
            default => 'gray',
        };
    }
}
