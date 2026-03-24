<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ropa_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('process_name');
            $table->string('process_code')->nullable()->comment('รหัสกิจกรรม');
            $table->string('department')->nullable();
            $table->string('process_owner')->nullable();
            $table->enum('role', ['controller', 'processor', 'joint_controller'])->default('controller');
            // วัตถุประสงค์
            $table->text('purpose');
            $table->enum('legal_basis', [
                'consent', 'contract', 'legal_obligation',
                'legitimate_interest', 'public_interest', 'vital_interest'
            ]);
            $table->text('legitimate_interest_description')->nullable();
            // ประเภทข้อมูล
            $table->json('data_categories')->comment('ประเภทข้อมูลส่วนบุคคล');
            $table->json('data_subject_types')->comment('ประเภทเจ้าของข้อมูล');
            $table->boolean('has_sensitive_data')->default(false);
            $table->json('sensitive_data_categories')->nullable();
            // ผู้รับข้อมูล
            $table->json('recipients')->nullable()->comment('หน่วยงานที่รับข้อมูล');
            $table->boolean('third_party_transfer')->default(false);
            $table->boolean('cross_border_transfer')->default(false);
            $table->text('cross_border_countries')->nullable();
            $table->text('cross_border_safeguards')->nullable();
            // ระยะเวลา
            $table->string('retention_period')->comment('เช่น 5 ปี, 10 ปี');
            $table->text('retention_criteria')->nullable();
            $table->text('deletion_method')->nullable();
            // ความปลอดภัย
            $table->json('security_measures')->nullable();
            $table->text('system_used')->nullable()->comment('ระบบที่ใช้ประมวลผล');
            // สถานะ
            $table->enum('status', ['draft', 'active', 'under_review', 'archived'])->default('draft');
            $table->date('last_reviewed_at')->nullable();
            $table->date('next_review_date')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['organization_id', 'status']);
            $table->index(['organization_id', 'department']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ropa_records');
    }
};
