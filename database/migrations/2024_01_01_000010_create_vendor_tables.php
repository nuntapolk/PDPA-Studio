<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('tax_id')->nullable();
            $table->string('website')->nullable();
            $table->string('country', 2)->default('TH');
            $table->string('contact_name')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone', 20)->nullable();
            $table->text('services_description');
            $table->json('data_types_shared')->comment('ประเภทข้อมูลที่แชร์');
            $table->enum('role', ['processor', 'controller', 'joint_controller', 'sub_processor'])->default('processor');
            $table->enum('risk_level', ['low', 'medium', 'high'])->default('medium');
            $table->boolean('is_cross_border')->default(false);
            $table->text('transfer_mechanism')->nullable()->comment('SCCs, adequacy decision, etc.');
            // DPA
            $table->boolean('dpa_signed')->default(false);
            $table->date('dpa_signed_at')->nullable();
            $table->date('dpa_expires_at')->nullable();
            $table->string('dpa_file_path')->nullable();
            $table->enum('status', ['active', 'inactive', 'under_review', 'terminated'])->default('active');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['organization_id', 'status']);
            $table->index(['dpa_expires_at']);
        });

        // Vendor Risk Assessment
        Schema::create('vendor_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assessed_by')->constrained('users');
            $table->integer('score')->default(0)->comment('0-100');
            $table->enum('risk_level', ['low', 'medium', 'high'])->default('medium');
            $table->json('questions_answers')->nullable();
            $table->text('findings')->nullable();
            $table->text('recommendations')->nullable();
            $table->date('next_assessment_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_assessments');
        Schema::dropIfExists('vendors');
    }
};
