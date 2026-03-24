<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('reference_id')->nullable()->comment('ID จากระบบภายนอก');
            $table->string('type')->default('customer')
                ->comment('customer|employee|prospect|patient|student|other');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('national_id', 13)->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('nationality')->nullable();
            $table->text('address')->nullable();
            $table->json('metadata')->nullable()->comment('ข้อมูลเพิ่มเติมจากระบบ Legacy');
            $table->enum('status', ['active', 'inactive', 'deleted'])->default('active');
            $table->timestamp('deleted_request_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['organization_id', 'email']);
            $table->index(['organization_id', 'reference_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('data_subjects');
    }
};
