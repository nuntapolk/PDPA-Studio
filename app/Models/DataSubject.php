<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DataSubject extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id', 'reference_id', 'type',
        'first_name', 'last_name', 'email', 'phone',
        'national_id', 'date_of_birth', 'nationality',
        'address', 'metadata', 'status', 'deleted_request_at',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'metadata' => 'array',
        'deleted_request_at' => 'datetime',
    ];

    protected $hidden = ['national_id'];

    // Relationships
    public function organization() { return $this->belongsTo(Organization::class); }
    public function consents() { return $this->hasMany(Consent::class); }
    public function rightsRequests() { return $this->hasMany(RightsRequest::class); }

    // Helpers
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function hasActiveConsent(int $templateId): bool
    {
        return $this->consents()
            ->where('template_id', $templateId)
            ->where('granted', true)
            ->whereNull('withdrawn_at')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->exists();
    }

    public function getTypeLabel(): string
    {
        return match($this->type) {
            'customer' => 'ลูกค้า',
            'employee' => 'พนักงาน',
            'prospect' => 'ผู้มุ่งหวัง',
            'patient' => 'ผู้ป่วย',
            'student' => 'นักเรียน/นักศึกษา',
            default => 'อื่นๆ',
        };
    }
}
