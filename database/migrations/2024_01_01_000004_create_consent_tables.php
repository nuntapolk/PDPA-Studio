<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Consent Templates
        Schema::create('consent_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->integer('version')->default(1);
            $table->text('purpose');
            $table->text('description')->nullable();
            $table->enum('legal_basis', [
                'consent',
                'contract',
                'legal_obligation',
                'legitimate_interest',
                'public_interest',
                'vital_interest'
            ])->default('consent');
            $table->integer('retention_days')->default(365);
            $table->text('data_categories')->nullable()->comment('ประเภทข้อมูลที่เก็บ');
            $table->boolean('is_sensitive')->default(false)->comment('ข้อมูลอ่อนไหว มาตรา 26');
            $table->boolean('requires_explicit_consent')->default(false);
            $table->text('withdrawal_info')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });

        // Consents (บันทึกการให้ความยินยอม)
        Schema::create('consents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('data_subject_id')->constrained()->cascadeOnDelete();
            $table->foreignId('template_id')->constrained('consent_templates');
            $table->integer('template_version');
            $table->enum('channel', ['web', 'mobile', 'paper', 'verbal', 'email', 'api'])->default('web');
            $table->boolean('granted')->default(true);
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->text('proof')->nullable()->comment('หลักฐานการยินยอม (URL, file path)');
            $table->timestamp('granted_at')->nullable();
            $table->timestamp('withdrawn_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->text('withdrawal_reason')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['organization_id', 'data_subject_id']);
            $table->index(['organization_id', 'template_id']);
            $table->index(['granted_at']);
            $table->index(['expires_at']);
        });

        // Cookie Consents
        Schema::create('cookie_consents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('visitor_id', 64)->comment('Anonymous visitor ID');
            $table->boolean('necessary')->default(true);
            $table->boolean('analytics')->default(false);
            $table->boolean('marketing')->default(false);
            $table->boolean('preferences')->default(false);
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('consented_at');
            $table->timestamp('updated_at')->nullable();

            $table->index(['organization_id', 'visitor_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cookie_consents');
        Schema::dropIfExists('consents');
        Schema::dropIfExists('consent_templates');
    }
};
