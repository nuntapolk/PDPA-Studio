<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL requires rebuild to alter ENUM
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM(
            'super_admin',
            'admin',
            'dpo',
            'staff',
            'auditor',
            'api_user',
            'editor',
            'reviewer'
        ) NOT NULL DEFAULT 'editor'");
    }

    public function down(): void
    {
        // revert editor/reviewer → staff/auditor before shrinking enum
        DB::statement("UPDATE users SET role = 'staff'    WHERE role = 'editor'");
        DB::statement("UPDATE users SET role = 'auditor'  WHERE role = 'reviewer'");

        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM(
            'super_admin',
            'admin',
            'dpo',
            'staff',
            'auditor',
            'api_user'
        ) NOT NULL DEFAULT 'staff'");
    }
};
