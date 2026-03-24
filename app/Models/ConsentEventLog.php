<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ConsentEventLog extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'organization_id','consent_id','data_subject_id',
        'data_subject_name','data_subject_email',
        'event_type','consent_purpose','consent_version','channel',
        'consent_text_snapshot','proof_reference',
        'ip_address','user_agent_hash','recorded_by','notes','event_at','created_at',
    ];
    protected $casts = [
        'event_at'   => 'datetime',
        'created_at' => 'datetime',
    ];
    public function organization() { return $this->belongsTo(Organization::class); }
    public function consent() { return $this->belongsTo(Consent::class); }
    public function dataSubject() { return $this->belongsTo(DataSubject::class); }
    public function recorder() { return $this->belongsTo(User::class, 'recorded_by'); }

    public static function eventColor(string $e): string
    {
        return match($e) {
            'granted'   => '#15572e',
            'renewed'   => '#0f3020',
            'withdrawn' => '#c0272d',
            'expired'   => '#d97706',
            'amended'   => '#1d4ed8',
            'rejected'  => '#7f1d1d',
            'imported'  => '#5b21b6',
            default     => '#64748b',
        };
    }
    public static function eventBg(string $e): string
    {
        return match($e) {
            'granted'   => '#dcfce7',
            'renewed'   => '#d1fae5',
            'withdrawn' => '#fee2e2',
            'expired'   => '#fef3c7',
            'amended'   => '#dbeafe',
            'rejected'  => '#fde8d8',
            'imported'  => '#ede9fe',
            default     => '#f1f5f9',
        };
    }
    public static function eventLabel(string $e): string
    {
        return match($e) {
            'granted'   => 'ให้ความยินยอม',
            'withdrawn' => 'ถอนความยินยอม',
            'expired'   => 'หมดอายุ',
            'renewed'   => 'ต่ออายุ',
            'amended'   => 'แก้ไข',
            'imported'  => 'นำเข้า',
            'rejected'  => 'ปฏิเสธ',
            default     => $e,
        };
    }
}
