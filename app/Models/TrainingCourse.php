<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrainingCourse extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id', 'title', 'description', 'content', 'thumbnail_path',
        'duration_minutes', 'is_required', 'passing_score', 'certificate_enabled',
        'validity_months', 'is_active', 'created_by',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'certificate_enabled' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function organization() { return $this->belongsTo(Organization::class); }
    public function questions() { return $this->hasMany(TrainingQuestion::class, 'course_id'); }
    public function completions() { return $this->hasMany(TrainingCompletion::class, 'course_id'); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }

    public function getCompletionRateAttribute(): float
    {
        $total = $this->organization->users()->count();
        if ($total === 0) return 0;
        $completed = $this->completions()->where('passed', true)->distinct('user_id')->count();
        return round(($completed / $total) * 100, 1);
    }

    public function hasUserCompleted(int $userId): bool
    {
        return $this->completions()
            ->where('user_id', $userId)
            ->where('passed', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>', now());
            })
            ->exists();
    }
}
