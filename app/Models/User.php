<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'organization_id', 'name', 'email', 'password',
        'phone', 'avatar_path', 'role',
        'mfa_secret', 'mfa_enabled', 'mfa_recovery_codes',
        'last_login_at', 'last_login_ip',
        'failed_login_attempts', 'locked_until', 'status',
        'is_builtin',
    ];

    protected $hidden = [
        'password', 'remember_token', 'mfa_secret', 'mfa_recovery_codes',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'locked_until' => 'datetime',
        'mfa_enabled' => 'boolean',
        'mfa_recovery_codes' => 'array',
        'password' => 'hashed',
    ];

    // Relationships
    public function organization() { return $this->belongsTo(Organization::class); }
    public function completions()  { return $this->hasMany(TrainingCompletion::class); }

    // Role Helpers
    public function isSuperAdmin(): bool { return $this->role === 'super_admin'; }
    public function isAdmin(): bool      { return in_array($this->role, ['super_admin', 'admin']); }
    public function isDpo(): bool        { return in_array($this->role, ['super_admin', 'admin', 'dpo']); }
    public function isEditor(): bool     { return in_array($this->role, ['super_admin', 'admin', 'dpo', 'editor', 'staff']); }
    public function isReviewer(): bool   { return in_array($this->role, ['super_admin', 'admin', 'dpo', 'reviewer', 'auditor']); }
    public function isAuditor(): bool    { return in_array($this->role, ['auditor', 'reviewer']); }
    public function isApiUser(): bool    { return $this->role === 'api_user'; }
    public function isBuiltin(): bool    { return (bool) $this->is_builtin; }

    public function canManage(): bool
    {
        return in_array($this->role, ['super_admin', 'admin', 'dpo', 'editor', 'staff']);
    }

    public function isLocked(): bool
    {
        return $this->status === 'locked' ||
            ($this->locked_until && $this->locked_until->isFuture());
    }

    public function getRoleLabel(): string
    {
        $roles = config('accounts.roles', []);
        if (isset($roles[$this->role])) {
            return $roles[$this->role]['label_th'] ?? $roles[$this->role]['label'];
        }
        return match($this->role) {
            'super_admin' => 'Super Admin',
            'admin'       => 'ผู้ดูแลระบบ',
            'dpo'         => 'DPO',
            'editor'      => 'เจ้าหน้าที่บันทึก',
            'reviewer'    => 'ผู้ตรวจสอบ',
            'staff'       => 'เจ้าหน้าที่',
            'auditor'     => 'ผู้ตรวจสอบ',
            'api_user'    => 'API User',
            default       => $this->role,
        };
    }

    public function getRoleColor(): string
    {
        $roles = config('accounts.roles', []);
        return $roles[$this->role]['color'] ?? '#64748b';
    }

    public function getRoleBg(): string
    {
        $roles = config('accounts.roles', []);
        return $roles[$this->role]['bg'] ?? '#f1f5f9';
    }

    public function getRoleIcon(): string
    {
        $roles = config('accounts.roles', []);
        return $roles[$this->role]['icon'] ?? '👤';
    }
}
