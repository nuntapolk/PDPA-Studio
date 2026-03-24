<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('breach_incidents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('incident_number')->unique()->comment('BR-2024-00001');
            $table->string('title');
            $table->text('description');
            $table->enum('breach_type', [
                'unauthorized_access',   // เข้าถึงโดยไม่ได้รับอนุญาต
                'accidental_disclosure', // เปิดเผยโดยไม่ตั้งใจ
                'data_theft',            // ถูกขโมยข้อมูล
                'ransomware',            // แรนซัมแวร์
                'system_vulnerability',  // ช่องโหว่ระบบ
                'human_error',           // ความผิดพลาดของคน
                'physical_breach',       // ละเมิดทางกายภาพ
                'other'
            ]);
            $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->enum('status', [
                'new',
                'investigating',
                'contained',
                'notifying_pdpc',
                'notifying_subjects',
                'resolved',
                'closed'
            ])->default('new');
            // รายละเอียดการละเมิด
            $table->timestamp('discovered_at');
            $table->timestamp('occurred_at')->nullable();
            $table->integer('affected_count')->default(0)->comment('จำนวนเจ้าของข้อมูลที่ได้รับผลกระทบ');
            $table->json('data_types_affected')->comment('ประเภทข้อมูลที่ได้รับผลกระทบ');
            $table->boolean('includes_sensitive_data')->default(false);
            $table->text('impact_assessment')->nullable();
            // การแจ้งเตือน
            $table->boolean('requires_pdpc_notification')->default(false);
            $table->timestamp('pdpc_notification_deadline')->nullable()->comment('72 ชั่วโมงหลังค้นพบ');
            $table->timestamp('pdpc_notified_at')->nullable();
            $table->string('pdpc_reference_number')->nullable();
            $table->boolean('requires_subject_notification')->default(false);
            $table->timestamp('subjects_notified_at')->nullable();
            // การแก้ไข
            $table->text('containment_actions')->nullable();
            $table->text('root_cause')->nullable();
            $table->text('corrective_actions')->nullable();
            $table->text('preventive_measures')->nullable();
            // ผู้รับผิดชอบ
            $table->foreignId('reported_by')->constrained('users');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['organization_id', 'status']);
            $table->index(['organization_id', 'severity']);
            $table->index(['pdpc_notification_deadline']);
        });

        // Timeline actions สำหรับ Breach
        Schema::create('breach_timelines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('breach_incident_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('action');
            $table->text('description')->nullable();
            $table->string('attachment_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('breach_timeline');
        Schema::dropIfExists('breach_incidents');
    }
};
