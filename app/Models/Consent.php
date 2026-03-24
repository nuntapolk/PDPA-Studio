<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consent extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id', 'data_subject_id', 'template_id', 'template_version',
        'channel', 'granted', 'ip_address', 'user_agent', 'proof',
        'granted_at', 'withdrawn_at', 'expires_at', 'withdrawal_reason', 'metadata',
    ];

    protected $casts = [
        'granted' => 'boolean',
        'granted_at' => 'datetime',
        'withdrawn_at' => 'datetime',
        'expires_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function organization() { return $this->belongsTo(Organization::class); }
    public function dataSubject() { return $this->belongsTo(DataSubject::class); }
    public function template() { return $this->belongsTo(ConsentTemplate::class, 'template_id'); }

    public function isActive(): bool
    {
        return $this->granted
            && is_null($this->withdrawn_at)
            && ($this->expires_at === null || $this->expires_at->isFuture());
    }

    public function getStatusLabel(): string
    {
        if ($this->withdrawn_at) return 'ถอนความยินยอม';
        if ($this->expires_at && $this->expires_at->isPast()) return 'หมดอายุ';
        if ($this->granted) return 'ยินยอม';
        return 'ไม่ยินยอม';
    }

    public function getChannelLabel(): string
    {
        return match($this->channel) {
            'web' => 'เว็บไซต์',
            'mobile' => 'Mobile App',
            'paper' => 'กระดาษ',
            'verbal' => 'วาจา',
            'email' => 'อีเมล',
            'api' => 'API',
            default => $this->channel,
        };
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('granted', true)
            ->whereNull('withdrawn_at')
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            });
    }

    public function scopeExpiringSoon($query, int $days = 30)
    {
        return $query->active()->where('expires_at', '<=', now()->addDays($days));
    }
}
