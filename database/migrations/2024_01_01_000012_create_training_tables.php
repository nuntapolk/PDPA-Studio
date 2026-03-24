<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('training_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->longText('content')->nullable()->comment('เนื้อหา HTML');
            $table->string('thumbnail_path')->nullable();
            $table->integer('duration_minutes')->default(30);
            $table->boolean('is_required')->default(false);
            $table->integer('passing_score')->default(80)->comment('คะแนนผ่าน (%)');
            $table->boolean('certificate_enabled')->default(true);
            $table->integer('validity_months')->default(12)->comment('อายุใบรับรอง');
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('training_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('training_courses')->cascadeOnDelete();
            $table->text('question');
            $table->json('options')->comment('ตัวเลือก A,B,C,D');
            $table->string('correct_answer');
            $table->text('explanation')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('training_completions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained('training_courses')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('score')->nullable()->comment('คะแนนที่ได้ (%)');
            $table->boolean('passed')->default(false);
            $table->integer('attempt_number')->default(1);
            $table->string('certificate_path')->nullable();
            $table->string('certificate_number')->nullable()->unique();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['course_id', 'user_id']);
        });

        // Policy Acknowledgement
        Schema::create('policy_acknowledgements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('privacy_notice_id')->constrained()->cascadeOnDelete();
            $table->timestamp('acknowledged_at');
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'privacy_notice_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('policy_acknowledgements');
        Schema::dropIfExists('training_completions');
        Schema::dropIfExists('training_questions');
        Schema::dropIfExists('training_courses');
    }
};
