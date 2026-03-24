<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class AssessmentSection extends Model
{
    protected $fillable = ['assessment_id','title','sort_order'];
    public function assessment() { return $this->belongsTo(Assessment::class); }
    public function questions()  { return $this->hasMany(AssessmentQuestion::class, 'section_id')->orderBy('sort_order'); }
}
