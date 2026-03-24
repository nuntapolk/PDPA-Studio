<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('tax_id', 13)->nullable();
            $table->string('industry')->nullable();
            $table->text('address')->nullable();
            $table->string('website')->nullable();
            $table->string('logo_path')->nullable();
            // DPO Info
            $table->string('dpo_name')->nullable();
            $table->string('dpo_email')->nullable();
            $table->string('dpo_phone', 20)->nullable();
            // Plan
            $table->enum('plan', ['free', 'starter', 'pro', 'enterprise'])->default('free');
            $table->enum('status', ['active', 'suspended', 'inactive'])->default('active');
            $table->integer('max_users')->default(5);
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('organizations');
    }
};
