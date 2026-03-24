<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rights_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('data_subject_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ticket_number')->unique()->comment('RR-2024-00001');
            $table->enum('type', [
                'access',        // สิทธิ์เข้าถึง มาตรา 30
                'rectification', // สิทธิ์แก้ไข มาตรา 35
                'erasure',       // สิทธิ์ลบ มาตรา 33
                'restriction',   // สิทธิ์ระงับ มาตรา 34
                'portability',   // สิทธิ์พกพา มาตรา 36
                'objection'      // สิทธิ์คัดค้าน มาตรา 32
            ]);
            $table->enum('status', [
                'pending',
                'in_review',
                'awaiting_info',
                'approved',
                'completed',
                'rejected',
                'withdrawn'
            ])->default('pending');
            // ข้อมูลผู้ยื่นคำร้อง (อาจไม่มีบัญชีในระบบ)
            $table->string('requester_name');
            $table->string('requester_email');
            $table->string('requester_phone', 20)->nullable();
            $table->string('requester_id_number', 13)->nullable()->comment('เลขบัตรประชาชน');
            $table->text('description');
            $table->text('data_scope')->nullable()->comment('ขอบเขตข้อมูลที่ร้องขอ');
            // การดำเนินการ
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('due_date')->nullable()->comment('ครบกำหนด 30 วัน');
            $table->text('response_note')->nullable();
            $table->string('response_file')->nullable();
            $table->text('rejection_reason')->nullable();
            // Timestamps
            $table->timestamp('submitted_at');
            $table->timestamp('acknowledged_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['organization_id', 'status']);
            $table->index(['organization_id', 'type']);
            $table->index(['due_date']);
        });

        // Timeline / Comments สำหรับ Rights Requests
        Schema::create('rights_request_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rights_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('note');
            $table->boolean('is_internal')->default(true)->comment('Internal note หรือส่งให้ผู้ร้องขอ');
            $table->string('attachment_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rights_request_notes');
        Schema::dropIfExists('rights_requests');
    }
};
