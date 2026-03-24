<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingCompletion extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id', 'user_id', 'score', 'passed', 'attempt_number',
        'certificate_path', 'certificate_number',
        'started_at', 'completed_at', 'expires_at',
    ];

    protected $casts = [
        'passed'       => 'boolean',
        'started_at'   => 'datetime',
        'completed_at' => 'datetime',
        'expires_at'   => 'datetime',
    ];

    public function course() { return $this->belongsTo(TrainingCourse::class); }
    public function user()   { return $this->belongsTo(User::class); }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isValid(): bool
    {
        return $this->passed && !$this->isExpired();
    }

    public static function generateCertNumber(int $courseId, int $userId): string
    {
        return 'CERT-'.date('Y').'-'.str_pad($courseId,3,'0',STR_PAD_LEFT)
               .'-'.str_pad($userId,4,'0',STR_PAD_LEFT)
               .'-'.strtoupper(substr(md5(uniqid()),0,6));
    }
}
