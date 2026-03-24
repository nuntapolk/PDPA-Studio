<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // ── 1. External Parties ──────────────────────────────────────────────
        Schema::create('external_parties', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->nullable()->comment('รหัสภายใน EP-0001');

            // Identity
            $table->string('name');
            $table->string('name_en')->nullable();
            $table->enum('type', ['company','individual','government','ngo','academic','other'])->default('company');
            $table->string('tax_id', 50)->nullable();
            $table->string('registration_no', 100)->nullable();
            $table->char('country', 2)->default('TH');
            $table->string('industry', 100)->nullable();

            // Relationship กับ YOUR ORG
            $table->enum('relationship_type', [
                'data_processor',       // เราคือ DC, เขาประมวลผลให้เรา
                'data_controller',      // เขาคือ DC, เราประมวลผลให้เขา
                'joint_controller',     // ควบคุมร่วมกัน
                'sub_processor',        // ประมวลผลต่อจาก Processor
                'recipient',            // เราเปิดเผยข้อมูลให้เขา
                'third_party',          // บุคคลที่สาม
                'supervisory_authority' // หน่วยงานกำกับดูแล
            ])->default('data_processor');
            $table->date('relationship_started_at')->nullable();
            $table->date('relationship_ended_at')->nullable();

            // Services / Scope
            $table->text('services_description')->nullable();
            $table->json('data_types_shared')->nullable()->comment('ประเภทข้อมูลที่แชร์');
            $table->json('processing_purposes')->nullable();
            $table->json('systems_involved')->nullable()->comment('ระบบ/แพลตฟอร์มที่เกี่ยวข้อง');

            // Risk
            $table->enum('risk_level', ['low','medium','high','critical'])->default('medium');
            $table->text('risk_notes')->nullable();

            // Cross-border
            $table->boolean('is_cross_border')->default(false);
            $table->enum('transfer_mechanism', [
                'adequacy_decision','scc','bcr',
                'explicit_consent','vital_interest',
                'public_interest','derogation','other'
            ])->nullable();
            $table->json('transfer_countries')->nullable();
            $table->boolean('tia_required')->default(false)->comment('Transfer Impact Assessment');
            $table->date('tia_completed_at')->nullable();

            // Contact
            $table->text('address')->nullable();
            $table->string('website')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 30)->nullable();
            $table->string('contact_name', 150)->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone', 30)->nullable();
            $table->string('dpo_name', 150)->nullable();
            $table->string('dpo_email')->nullable();
            $table->string('dpo_phone', 30)->nullable();

            // Status
            $table->enum('status', ['active','inactive','under_review','suspended','terminated'])->default('active');
            $table->unsignedTinyInteger('review_frequency_months')->default(12);
            $table->date('next_review_date')->nullable();
            $table->text('notes')->nullable();

            // Meta
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['relationship_type', 'status']);
            $table->index(['is_cross_border']);
            $table->index(['next_review_date']);
            $table->index(['risk_level', 'status']);
        });

        // ── 2. Data Processing Agreements ────────────────────────────────────
        Schema::create('data_processing_agreements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('external_party_id')->constrained()->cascadeOnDelete();
            $table->string('dpa_number', 50)->nullable();
            $table->string('title');
            $table->enum('type', ['dpa','jca','addendum','nda','data_sharing_agreement'])->default('dpa');

            // Parties roles
            $table->enum('our_role', ['controller','processor','joint_controller'])->default('controller');
            $table->enum('their_role', ['controller','processor','joint_controller'])->default('processor');
            $table->string('signatory_our', 150)->nullable();
            $table->string('signatory_their', 150)->nullable();

            // Validity
            $table->enum('status', ['draft','pending_signature','active','expired','terminated','superseded'])->default('draft');
            $table->date('signed_at')->nullable();
            $table->date('effective_at')->nullable();
            $table->date('expires_at')->nullable();
            $table->boolean('auto_renew')->default(false);
            $table->unsignedSmallInteger('termination_notice_days')->default(30);

            // Scope
            $table->json('data_categories')->nullable();
            $table->json('processing_purposes')->nullable();
            $table->boolean('sub_processors_allowed')->default(false);
            $table->text('security_requirements')->nullable();
            $table->boolean('audit_rights')->default(true);
            $table->unsignedSmallInteger('breach_notification_hours')->default(72);

            // Documents
            $table->string('file_path')->nullable();
            $table->string('file_hash', 64)->nullable()->comment('SHA256');
            $table->string('version', 20)->nullable();
            $table->foreignId('supersedes_id')->nullable()->constrained('data_processing_agreements')->nullOnDelete();

            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['external_party_id', 'status']);
            $table->index(['expires_at']);
            $table->index(['status', 'expires_at']);
        });

        // ── 3. External Party Assessments ─────────────────────────────────────
        Schema::create('external_party_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('external_party_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assessed_by')->constrained('users');
            $table->enum('assessment_type', ['initial','periodic','triggered','post_incident'])->default('initial');
            $table->unsignedSmallInteger('score')->default(0)->comment('0-100');
            $table->enum('risk_level', ['low','medium','high','critical'])->default('medium');
            $table->json('questions_answers')->nullable();
            $table->text('findings')->nullable();
            $table->text('recommendations')->nullable();
            $table->text('corrective_actions')->nullable();
            $table->boolean('follow_up_required')->default(false);
            $table->date('follow_up_date')->nullable();
            $table->date('next_assessment_date')->nullable();
            $table->timestamps();

            $table->index(['external_party_id', 'created_at']);
        });

        // ── 4. ROPA ↔ External Parties pivot ─────────────────────────────────
        Schema::create('ropa_external_parties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ropa_record_id')->constrained()->cascadeOnDelete();
            $table->foreignId('external_party_id')->constrained()->cascadeOnDelete();
            $table->enum('party_role', ['recipient','processor','source','joint_controller'])->default('recipient');
            $table->json('data_categories')->nullable();
            $table->text('purpose')->nullable();
            $table->string('transfer_mechanism', 50)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['ropa_record_id','external_party_id','party_role'], 'ropa_ep_unique');
            $table->index(['external_party_id']);
        });

        // ── 5. Consent Template ↔ External Parties pivot ──────────────────────
        Schema::create('consent_external_parties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consent_template_id')->constrained()->cascadeOnDelete();
            $table->foreignId('external_party_id')->constrained()->cascadeOnDelete();
            $table->text('disclosure_purpose')->nullable();
            $table->json('data_categories')->nullable();
            $table->boolean('is_mandatory')->default(false);
            $table->timestamps();

            $table->unique(['consent_template_id','external_party_id'], 'cep_tmpl_party_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consent_external_parties');
        Schema::dropIfExists('ropa_external_parties');
        Schema::dropIfExists('external_party_assessments');
        Schema::dropIfExists('data_processing_agreements');
        Schema::dropIfExists('external_parties');
    }
};
