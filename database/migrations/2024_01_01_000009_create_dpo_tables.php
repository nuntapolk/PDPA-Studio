<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // DPO Tasks
        Schema::create('dpo_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('category', [
                'compliance_review',  // ทบทวนความสอดคล้อง
                'policy_update',      // อัปเดตนโยบาย
                'training',           // จัดอบรม
                'audit',              // ตรวจสอบ
                'vendor_review',      // ทบทวน Vendor
                'incident_response',  // ตอบสนองเหตุการณ์
                'reporting',          // รายงาน
                'other'
            ])->default('other');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->date('due_date')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->constrained('users');
            $table->text('notes')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['organization_id', 'status']);
            $table->index(['organization_id', 'due_date']);
        });

        // Compliance Checklist
        Schema::create('compliance_checklists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('category')->comment('consent|rights|ropa|breach|security|policy|training|vendor');
            $table->string('item');
            $table->text('description')->nullable();
            $table->string('reference')->nullable()->comment('อ้างอิงมาตรา PDPA');
            $table->enum('status', ['not_started', 'in_progress', 'completed', 'na'])->default('not_started');
            $table->string('evidence_path')->nullable();
            $table->text('notes')->nullable();
            $table->date('due_date')->nullable();
            $table->date('completed_at')->nullable();
            $table->foreignId('responsible_user')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index(['organization_id', 'category']);
            $table->index(['organization_id', 'status']);
        });

        // DPO Documents Repository
        Schema::create('dpo_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('category', [
                'policy', 'procedure', 'form', 'template',
                'certificate', 'report', 'contract', 'other'
            ])->default('other');
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_type', 10);
            $table->bigInteger('file_size')->default(0);
            $table->integer('version')->default(1);
            $table->date('valid_until')->nullable();
            $table->foreignId('uploaded_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dpo_documents');
        Schema::dropIfExists('compliance_checklists');
        Schema::dropIfExists('dpo_tasks');
    }
};
