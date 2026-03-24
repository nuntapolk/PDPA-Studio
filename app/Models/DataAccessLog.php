<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class DataAccessLog extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'organization_id','user_id','user_name',
        'access_type','data_category','table_name','record_id',
        'fields_accessed','record_count','purpose','legal_basis',
        'recipient','is_cross_border','destination_country',
        'ip_address','request_id','created_at',
    ];
    protected $casts = [
        'fields_accessed' => 'array',
        'is_cross_border' => 'boolean',
        'created_at'      => 'datetime',
    ];
    public function organization() { return $this->belongsTo(Organization::class); }
    public function user() { return $this->belongsTo(User::class); }

    public static function accessTypeLabel(string $t): string
    {
        return match($t) {
            'read'         => 'อ่าน',
            'search'       => 'ค้นหา',
            'export'       => 'Export',
            'print'        => 'พิมพ์',
            'share'        => 'แชร์',
            'api'          => 'API',
            'bulk_export'  => 'Bulk Export',
            'rights_request' => 'ตามคำขอสิทธิ์',
            default        => $t,
        };
    }
    public static function categoryColor(string $c): string
    {
        return match($c) {
            'sensitive','biometric','health' => '#c0272d',
            'financial'                      => '#d97706',
            'personal'                       => '#1d4ed8',
            default                          => '#64748b',
        };
    }

    public static function record(
        string  $accessType,
        string  $dataCategory,
        string  $tableName,
        ?int    $recordId    = null,
        array   $fields      = [],
        int     $count       = 1,
        ?string $purpose     = null,
        ?string $legalBasis  = null
    ): void {
        $user = auth()->user();
        if (!$user) return;
        static::create([
            'organization_id' => $user->organization_id,
            'user_id'         => $user->id,
            'user_name'       => $user->name,
            'access_type'     => $accessType,
            'data_category'   => $dataCategory,
            'table_name'      => $tableName,
            'record_id'       => $recordId,
            'fields_accessed' => $fields ?: null,
            'record_count'    => $count,
            'purpose'         => $purpose,
            'legal_basis'     => $legalBasis,
            'ip_address'      => request()->ip(),
            'request_id'      => \Illuminate\Support\Str::uuid(),
            'created_at'      => now(),
        ]);
    }
}
