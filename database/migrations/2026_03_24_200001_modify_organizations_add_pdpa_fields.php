<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            // PDPA Role
            $table->enum('primary_pdpa_role', ['controller','processor','both'])
                  ->default('both')->after('industry');
            $table->string('pdpa_registration_no', 50)->nullable()->after('primary_pdpa_role');
            $table->date('pdpa_certified_at')->nullable()->after('pdpa_registration_no');
            // DPO extended
            $table->date('dpo_appointed_at')->nullable()->after('dpo_phone');
            $table->boolean('dpo_is_external')->default(false)->after('dpo_appointed_at');
            // Contact
            $table->string('legal_rep_name', 150)->nullable()->after('dpo_is_external');
            $table->string('privacy_email')->nullable()->after('legal_rep_name');
            $table->string('privacy_phone', 30)->nullable()->after('privacy_email');
            // Settings
            $table->json('settings')->nullable()->after('privacy_phone');
        });

        Schema::table('breach_incidents', function (Blueprint $table) {
            $table->foreignId('source_party_id')
                  ->nullable()->after('organization_id')
                  ->constrained('external_parties')->nullOnDelete()
                  ->comment('External party ที่เป็นต้นเหตุหรือเกี่ยวข้อง');
            $table->json('involved_party_ids')
                  ->nullable()->after('source_party_id')
                  ->comment('FK list ของ external_parties ที่ได้รับผลกระทบ');
        });
    }

    public function down(): void
    {
        Schema::table('breach_incidents', function (Blueprint $table) {
            $table->dropForeign(['source_party_id']);
            $table->dropColumn(['source_party_id','involved_party_ids']);
        });
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn([
                'primary_pdpa_role','pdpa_registration_no','pdpa_certified_at',
                'dpo_appointed_at','dpo_is_external',
                'legal_rep_name','privacy_email','privacy_phone','settings',
            ]);
        });
    }
};
