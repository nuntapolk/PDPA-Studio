<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('assessment_number')->unique()->comment('DPIA-2024-001');
            $table->enum('type', ['dpia', 'lia', 'gap_analysis'])->default('dpia');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('scope')->nullable()->comment('ขอบเขตของการประเมิน');
            $table->enum('status', ['draft', 'in_progress', 'completed', 'approved', 'archived'])->default('draft');
            $table->enum('risk_level', ['low', 'medium', 'high', 'very_high'])->nullable();
            $table->integer('risk_score')->nullable()->comment('0-100');
            $table->text('findings')->nullable();
            $table->text('recommendations')->nullable();
            $table->text('mitigation_measures')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['organization_id', 'type', 'status']);
        });

        Schema::create('assessment_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('assessment_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('section_id')->nullable()->constrained('assessment_sections')->nullOnDelete();
            $table->text('question');
            $table->enum('answer_type', ['text', 'yes_no', 'scale', 'multiple_choice'])->default('text');
            $table->text('answer')->nullable();
            $table->integer('risk_score')->default(0)->comment('0-10');
            $table->text('notes')->nullable();
            $table->json('options')->nullable()->comment('สำหรับ multiple_choice');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_questions');
        Schema::dropIfExists('assessment_sections');
        Schema::dropIfExists('assessments');
    }
};
