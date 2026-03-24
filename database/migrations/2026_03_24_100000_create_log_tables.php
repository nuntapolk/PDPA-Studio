<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ─────────────────────────────────────────────────────────────────────
        // 1. OPERATION LOG — ทุก HTTP request/response
        // ─────────────────────────────────────────────────────────────────────
        Schema::create('operation_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('user_name', 100)->nullable()->comment('snapshot ชื่อ user');
            $table->string('method', 10)->comment('GET|POST|PUT|PATCH|DELETE');
            $table->string('url', 500);
            $table->string('route_name', 150)->nullable()->comment('named route เช่น ropa.show');
            $table->string('route_action', 150)->nullable()->comment('Controller@method');
            $table->smallInteger('status_code')->comment('HTTP response code');
            $table->unsignedInteger('duration_ms')->comment('เวลาตอบสนอง ms');
            $table->unsignedInteger('memory_mb')->nullable()->comment('peak memory MB');
            $table->unsignedInteger('request_size')->nullable()->comment('bytes');
            $table->unsignedInteger('response_size')->nullable()->comment('bytes');
            $table->string('ip_address', 45)->nullable();
            $table->string('session_id', 100)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('referer', 500)->nullable();
            $table->timestamp('created_at');

            $table->index(['organization_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['route_name', 'created_at']);
            $table->index(['status_code', 'created_at']);
            $table->index(['duration_ms']); // หา slow requests
        });

        // ─────────────────────────────────────────────────────────────────────
        // 2. SECURITY LOG — security events
        // ─────────────────────────────────────────────────────────────────────
        Schema::create('security_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('user_name', 100)->nullable()->comment('snapshot หรือ attempted email');
            $table->string('event_type', 60)->comment(
                'login_failed|login_success|logout|account_locked|account_unlocked|'.
                'mfa_enabled|mfa_disabled|mfa_verified|mfa_failed|'.
                'password_changed|password_reset|'.
                'permission_denied|session_expired|token_created|token_revoked|'.
                'suspicious_ip|brute_force_detected|data_export'
            );
            $table->enum('severity', ['low','medium','high','critical'])->default('medium');
            $table->string('description', 500)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('metadata')->nullable()->comment('additional context เช่น fail_count, attempted_resource');
            $table->boolean('is_resolved')->default(false);
            $table->timestamp('resolved_at')->nullable();
            $table->string('resolved_by', 100)->nullable();
            $table->timestamp('created_at');

            $table->index(['organization_id', 'event_type', 'created_at']);
            $table->index(['organization_id', 'severity', 'created_at']);
            $table->index(['ip_address', 'event_type']);
            $table->index(['user_id', 'created_at']);
            $table->index(['is_resolved', 'severity']);
        });

        // ─────────────────────────────────────────────────────────────────────
        // 3. DATA ACCESS LOG — เข้าถึงข้อมูลส่วนบุคคล (PDPA ม.40)
        // ─────────────────────────────────────────────────────────────────────
        Schema::create('data_access_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('user_name', 100)->nullable()->comment('snapshot');
            $table->string('access_type', 30)->comment('read|search|export|print|share|api|bulk_export|rights_request');
            $table->string('data_category', 60)->comment('personal|sensitive|financial|health|biometric|...');
            $table->string('table_name', 80)->nullable()->comment('ตาราง DB ที่เข้าถึง');
            $table->unsignedBigInteger('record_id')->nullable()->comment('row id ที่เข้าถึง');
            $table->json('fields_accessed')->nullable()->comment('รายชื่อ field ที่อ่าน');
            $table->unsignedInteger('record_count')->default(1)->comment('จำนวน record');
            $table->string('purpose', 200)->nullable()->comment('วัตถุประสงค์การเข้าถึง');
            $table->string('legal_basis', 60)->nullable()->comment('consent|contract|legal_obligation|...');
            $table->string('recipient', 200)->nullable()->comment('ผู้รับข้อมูล (ถ้าเปิดเผย)');
            $table->boolean('is_cross_border')->default(false)->comment('ส่งข้อมูลข้ามพรมแดน');
            $table->string('destination_country', 60)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('request_id', 36)->nullable()->comment('UUID สำหรับ trace');
            $table->timestamp('created_at');

            $table->index(['organization_id', 'created_at']);
            $table->index(['organization_id', 'user_id', 'created_at']);
            $table->index(['organization_id', 'data_category', 'access_type']);
            $table->index(['table_name', 'record_id']);
            $table->index(['is_cross_border', 'created_at']);
        });

        // ─────────────────────────────────────────────────────────────────────
        // 4. CONSENT EVENT LOG — ประวัติ consent ทุกการเปลี่ยนแปลง (Legal)
        // ─────────────────────────────────────────────────────────────────────
        Schema::create('consent_event_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('consent_id')->nullable()->constrained('consents')->nullOnDelete();
            $table->foreignId('data_subject_id')->nullable()->constrained('data_subjects')->nullOnDelete();
            $table->string('data_subject_name', 150)->nullable()->comment('snapshot');
            $table->string('data_subject_email', 150)->nullable()->comment('snapshot');
            $table->string('event_type', 40)->comment('granted|withdrawn|expired|renewed|amended|imported|rejected');
            $table->string('consent_purpose', 200)->nullable()->comment('วัตถุประสงค์ที่ให้/ถอน');
            $table->string('consent_version', 20)->nullable()->comment('เวอร์ชัน Privacy Notice');
            $table->string('channel', 40)->nullable()->comment('web|api|paper|email|in_person|import');
            $table->text('consent_text_snapshot')->nullable()->comment('ข้อความที่ยินยอม ณ เวลานั้น');
            $table->string('proof_reference', 200)->nullable()->comment('เลขอ้างอิง, URL, ไฟล์');
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent_hash', 64)->nullable()->comment('hash สำหรับ verify device');
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('notes', 500)->nullable();
            $table->timestamp('event_at')->comment('เวลาเกิดเหตุการณ์จริง');
            $table->timestamp('created_at');

            $table->index(['organization_id', 'event_type', 'event_at']);
            $table->index(['data_subject_id', 'event_at']);
            $table->index(['consent_id', 'event_at']);
            $table->index(['organization_id', 'created_at']);
        });

        // ─────────────────────────────────────────────────────────────────────
        // 5. SYSTEM ERROR LOG — exceptions, errors, failed jobs
        // ─────────────────────────────────────────────────────────────────────
        Schema::create('system_error_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('level', ['debug','info','notice','warning','error','critical','alert','emergency'])->default('error');
            $table->string('channel', 50)->default('app')->comment('app|queue|api|auth|scheduler|...');
            $table->string('message', 1000);
            $table->string('exception_class', 200)->nullable()->comment('Fully qualified exception class');
            $table->string('file', 300)->nullable();
            $table->unsignedInteger('line')->nullable();
            $table->text('stack_trace')->nullable();
            $table->json('context')->nullable()->comment('request data, user context');
            $table->string('request_url', 500)->nullable();
            $table->string('request_method', 10)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('environment', 20)->default('production');
            $table->string('app_version', 20)->nullable();
            $table->boolean('is_resolved')->default(false);
            $table->timestamp('resolved_at')->nullable();
            $table->string('resolved_by', 100)->nullable();
            $table->string('resolution_note', 500)->nullable();
            $table->unsignedInteger('occurrence_count')->default(1)->comment('จำนวนครั้งที่เกิดซ้ำ');
            $table->timestamp('last_occurred_at')->nullable();
            $table->timestamp('created_at');

            $table->index(['level', 'created_at']);
            $table->index(['channel', 'level', 'created_at']);
            $table->index(['is_resolved', 'level', 'created_at']);
            $table->index(['exception_class']);
            $table->index(['organization_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_error_logs');
        Schema::dropIfExists('consent_event_logs');
        Schema::dropIfExists('data_access_logs');
        Schema::dropIfExists('security_logs');
        Schema::dropIfExists('operation_logs');
    }
};
