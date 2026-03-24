<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('privacy_notices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->enum('type', [
                'privacy_policy',    // นโยบายความเป็นส่วนตัว
                'cookie_policy',     // นโยบาย Cookie
                'employee_notice',   // ประกาศพนักงาน
                'cctv_notice',       // ประกาศกล้องวงจรปิด
                'marketing_notice',  // ประกาศการตลาด
                'third_party_notice' // ประกาศบุคคลที่สาม
            ]);
            $table->string('title');
            $table->string('language', 5)->default('th')->comment('th|en');
            $table->integer('version')->default(1);
            $table->longText('content');
            $table->string('effective_date')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(false);
            $table->string('public_url')->nullable()->comment('URL สาธารณะสำหรับแสดง');
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['organization_id', 'type', 'language']);
        });

        // Cookie Banner Settings
        Schema::create('cookie_banner_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete()->unique();
            $table->string('title')->default('เราใช้คุกกี้');
            $table->text('description');
            $table->string('accept_btn_text')->default('ยอมรับทั้งหมด');
            $table->string('reject_btn_text')->default('ปฏิเสธทั้งหมด');
            $table->string('settings_btn_text')->default('ตั้งค่า');
            $table->string('position', 20)->default('bottom')->comment('bottom|top|bottom-left|bottom-right');
            $table->string('theme', 20)->default('light')->comment('light|dark');
            $table->string('primary_color', 7)->default('#2563EB');
            $table->boolean('show_analytics')->default(true);
            $table->boolean('show_marketing')->default(true);
            $table->boolean('show_preferences')->default(true);
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cookie_banner_settings');
        Schema::dropIfExists('privacy_notices');
    }
};
