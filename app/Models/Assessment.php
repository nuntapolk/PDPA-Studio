<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Assessment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id', 'assessment_number', 'type', 'title', 'description', 'scope',
        'status', 'risk_level', 'risk_score', 'findings', 'recommendations', 'mitigation_measures',
        'created_by', 'approved_by', 'started_at', 'completed_at', 'approved_at', 'next_review_date',
    ];

    protected $casts = [
        'started_at'       => 'datetime',
        'completed_at'     => 'datetime',
        'approved_at'      => 'datetime',
        'next_review_date' => 'date',
    ];

    public function organization() { return $this->belongsTo(Organization::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function approver() { return $this->belongsTo(User::class, 'approved_by'); }
    public function sections() { return $this->hasMany(AssessmentSection::class); }
    public function questions() { return $this->hasMany(AssessmentQuestion::class); }

    public function getTypeLabel(): string
    {
        return match($this->type) {
            'dpia' => 'DPIA - ประเมินผลกระทบ',
            'lia' => 'LIA - ประโยชน์อันชอบธรรม',
            'gap_analysis' => 'Gap Analysis',
            default => $this->type,
        };
    }

    public function getRiskColor(): string
    {
        return match($this->risk_level) {
            'low' => 'green',
            'medium' => 'yellow',
            'high' => 'orange',
            'very_high' => 'red',
            default => 'gray',
        };
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (!$model->assessment_number) {
                $prefix = strtoupper($model->type);
                $count = static::where('type', $model->type)->whereYear('created_at', now()->year)->count() + 1;
                $model->assessment_number = $prefix . '-' . now()->year . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);
            }
        });
    }
}
