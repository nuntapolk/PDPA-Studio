<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class AssessmentQuestion extends Model
{
    protected $fillable = ['assessment_id','section_id','question','answer_type','answer','risk_score','notes','options','sort_order'];
    protected $casts    = ['options' => 'array'];
    public function assessment() { return $this->belongsTo(Assessment::class); }
    public function section()    { return $this->belongsTo(AssessmentSection::class); }
}
