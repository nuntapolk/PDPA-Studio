<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ExternalPartyAssessment extends Model
{
    protected $fillable = [
        'external_party_id','assessed_by','assessment_type',
        'score','risk_level','questions_answers',
        'findings','recommendations','corrective_actions',
        'follow_up_required','follow_up_date','next_assessment_date',
    ];
    protected $casts = [
        'questions_answers'   => 'array',
        'follow_up_required'  => 'boolean',
        'follow_up_date'      => 'date',
        'next_assessment_date'=> 'date',
    ];
    public function externalParty() { return $this->belongsTo(ExternalParty::class); }
    public function assessor() { return $this->belongsTo(User::class, 'assessed_by'); }

    public static function riskColor(string $r): string {
        return match($r) {
            'low'=>'#15572e','medium'=>'#d97706','high'=>'#dc2626','critical'=>'#7f1d1d', default=>'#64748b',
        };
    }
}
