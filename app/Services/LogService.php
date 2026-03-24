<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\ConsentEventLog;
use App\Models\DataAccessLog;
use App\Models\SecurityLog;
use App\Models\SystemErrorLog;
use Illuminate\Support\Facades\Log;

/**
 * LogService — ศูนย์กลางการบันทึก Log ทุกประเภท
 *
 * การใช้งาน:
 *   LogService::audit('updated', 'ropa', $ropa)
 *   LogService::security('login_failed', 'high', 'Wrong password', ['email' => $email])
 *   LogService::dataAccess('export', 'personal', 'consents', null, [], 100, 'Marketing')
 *   LogService::consent($consent, 'granted', 'web')
 *   LogService::error($exception, ['request' => ...])
 */
class LogService
{
    // ── 1. AUDIT LOG ──────────────────────────────────────────────────────────
    public static function audit(
        string $action,
        string $module,
        mixed  $entity     = null,
        array  $oldValues  = [],
        array  $newValues  = []
    ): void {
        try {
            AuditLog::record($action, $module, $entity, $oldValues, $newValues);
        } catch (\Throwable $e) {
            Log::error('LogService::audit failed: '.$e->getMessage());
        }
    }

    // ── 2. SECURITY LOG ───────────────────────────────────────────────────────
    public static function security(
        string  $eventType,
        string  $severity   = 'medium',
        ?string $description = null,
        array   $metadata   = []
    ): void {
        try {
            SecurityLog::record($eventType, $severity, $description, $metadata);
        } catch (\Throwable $e) {
            Log::error('LogService::security failed: '.$e->getMessage());
        }
    }

    // ── 3. DATA ACCESS LOG ────────────────────────────────────────────────────
    public static function dataAccess(
        string  $accessType,
        string  $dataCategory,
        string  $tableName,
        ?int    $recordId   = null,
        array   $fields     = [],
        int     $count      = 1,
        ?string $purpose    = null,
        ?string $legalBasis = null
    ): void {
        try {
            DataAccessLog::record($accessType, $dataCategory, $tableName, $recordId, $fields, $count, $purpose, $legalBasis);
        } catch (\Throwable $e) {
            Log::error('LogService::dataAccess failed: '.$e->getMessage());
        }
    }

    // ── 4. CONSENT EVENT LOG ──────────────────────────────────────────────────
    public static function consent(
        mixed   $consent,
        string  $eventType,
        string  $channel    = 'web',
        ?string $notes      = null,
        array   $extra      = []
    ): void {
        try {
            $user = auth()->user();
            ConsentEventLog::create([
                'organization_id'       => $user?->organization_id ?? $consent?->organization_id,
                'consent_id'            => $consent?->id,
                'data_subject_id'       => $consent?->data_subject_id,
                'data_subject_name'     => $consent?->dataSubject?->name ?? ($extra['subject_name'] ?? null),
                'data_subject_email'    => $consent?->dataSubject?->email ?? ($extra['subject_email'] ?? null),
                'event_type'            => $eventType,
                'consent_purpose'       => $consent?->purpose ?? ($extra['purpose'] ?? null),
                'consent_version'       => $extra['version'] ?? null,
                'channel'               => $channel,
                'consent_text_snapshot' => $extra['text'] ?? null,
                'proof_reference'       => $extra['proof'] ?? null,
                'ip_address'            => request()->ip(),
                'user_agent_hash'       => hash('sha256', request()->userAgent() ?? ''),
                'recorded_by'           => $user?->id,
                'notes'                 => $notes,
                'event_at'              => now(),
                'created_at'            => now(),
            ]);
        } catch (\Throwable $e) {
            Log::error('LogService::consent failed: '.$e->getMessage());
        }
    }

    // ── 5. SYSTEM ERROR LOG ───────────────────────────────────────────────────
    public static function error(\Throwable $e, array $context = []): void
    {
        try {
            SystemErrorLog::capture($e, $context);
        } catch (\Throwable $inner) {
            Log::critical('LogService::error failed: '.$inner->getMessage());
            Log::error($e); // fallback to file
        }
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /** บันทึก login ล้มเหลว + อัปเดต fail count */
    public static function loginFailed(string $email, int $failCount): void
    {
        $severity = match(true) {
            $failCount >= 10 => 'critical',
            $failCount >= 5  => 'high',
            $failCount >= 3  => 'medium',
            default          => 'low',
        };
        static::security('login_failed', $severity, "Login failed (attempt #{$failCount}) for {$email}", [
            'attempted_email' => $email,
            'fail_count'      => $failCount,
        ]);
    }

    /** บันทึก export ข้อมูลส่วนบุคคล */
    public static function dataExport(string $module, int $recordCount, string $format = 'csv'): void
    {
        static::security('data_export', 'medium', "Exported {$recordCount} {$module} records as {$format}", [
            'module' => $module,
            'count'  => $recordCount,
            'format' => $format,
        ]);
        static::dataAccess('export', 'personal', $module, null, [], $recordCount, 'Export');
    }

    /** diff อัตโนมัติสำหรับ update */
    public static function auditUpdate(string $module, mixed $entity, array $before, array $after): void
    {
        $changed = array_filter($after, fn($v, $k) => isset($before[$k]) && $before[$k] !== $v, ARRAY_FILTER_USE_BOTH);
        $old = array_intersect_key($before, $changed);
        static::audit('updated', $module, $entity, $old, $changed);
    }
}
