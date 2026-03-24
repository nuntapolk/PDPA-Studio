<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrainingQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id', 'question', 'options', 'correct_answer', 'explanation', 'sort_order',
    ];

    protected $casts = [
        'options' => 'array',
    ];

    public function course() { return $this->belongsTo(TrainingCourse::class); }

    public function isCorrect(string $answer): bool
    {
        return strtoupper(trim($answer)) === strtoupper(trim($this->correct_answer));
    }
}
