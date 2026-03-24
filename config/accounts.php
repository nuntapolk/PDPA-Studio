<?php

/**
 * PDPA Studio — Built-in Account Configuration
 *
 * รหัสผ่านจะถูก Hash ด้วย bcrypt ตอน seed
 * เปลี่ยน password ที่นี่แล้วรัน: php artisan db:seed --class=UserSeeder
 *
 * ⚠️  ก่อน deploy production ให้เปลี่ยน password ทุก account
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Role Definitions
    |--------------------------------------------------------------------------
    | label, description, color, permissions
    */
    'roles' => [
        'admin' => [
            'label'       => 'Admin',
            'label_th'    => 'ผู้ดูแลระบบ',
            'description' => 'จัดการระบบ บัญชีผู้ใช้ และการตั้งค่าทั้งหมด',
            'color'       => '#c0272d',
            'bg'          => '#fee2e2',
            'icon'        => '🛡️',
        ],
        'editor' => [
            'label'       => 'Editor',
            'label_th'    => 'เจ้าหน้าที่บันทึก',
            'description' => 'สร้างและแก้ไขข้อมูล PDPA เช่น Consent, ROPA, Privacy Notice',
            'color'       => '#1d4ed8',
            'bg'          => '#dbeafe',
            'icon'        => '✏️',
        ],
        'dpo' => [
            'label'       => 'DPO',
            'label_th'    => 'เจ้าหน้าที่คุ้มครองข้อมูล',
            'description' => 'Data Protection Officer — ดูแลการปฏิบัติตาม PDPA',
            'color'       => '#15572e',
            'bg'          => '#dcfce7',
            'icon'        => '🔒',
        ],
        'reviewer' => [
            'label'       => 'Reviewer',
            'label_th'    => 'ผู้ตรวจสอบ',
            'description' => 'ตรวจสอบและอนุมัติ — อ่านได้อย่างเดียว ไม่แก้ไข',
            'color'       => '#7c3aed',
            'bg'          => '#ede9fe',
            'icon'        => '🔍',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Built-in Accounts (12 accounts: 2 Admin + 10 Built-in)
    |--------------------------------------------------------------------------
    | password — plain text, จะถูก Hash::make() ตอน seed
    | is_builtin — true = ไม่สามารถลบได้จากหน้า Account Setup
    */
    'users' => [

        // ── Admin (2) ─────────────────────────────────────────────────────
        [
            'name'       => 'System Administrator',
            'email'      => 'admin@pdpa.local',
            'role'       => 'admin',
            'password'   => env('SEED_PASSWORD_ADMIN', 'Admin@2025!'),
            'is_builtin' => true,
        ],
        [
            'name'       => 'Nuntapol',
            'email'      => 'nuntapol@pdpa.local',
            'role'       => 'admin',
            'password'   => env('SEED_PASSWORD_NUNTAPOL', 'Nuntapol@2025!'),
            'is_builtin' => true,
        ],

        // ── Editor (3) ────────────────────────────────────────────────────
        [
            'name'       => 'Editor 01',
            'email'      => 'editor01@pdpa.local',
            'role'       => 'editor',
            'password'   => env('SEED_PASSWORD_DEFAULT', 'Pdpa@2025!'),
            'is_builtin' => true,
        ],
        [
            'name'       => 'Editor 02',
            'email'      => 'editor02@pdpa.local',
            'role'       => 'editor',
            'password'   => env('SEED_PASSWORD_DEFAULT', 'Pdpa@2025!'),
            'is_builtin' => true,
        ],
        [
            'name'       => 'Editor 03',
            'email'      => 'editor03@pdpa.local',
            'role'       => 'editor',
            'password'   => env('SEED_PASSWORD_DEFAULT', 'Pdpa@2025!'),
            'is_builtin' => true,
        ],

        // ── DPO (3) ───────────────────────────────────────────────────────
        [
            'name'       => 'DPO 01',
            'email'      => 'dpo01@pdpa.local',
            'role'       => 'dpo',
            'password'   => env('SEED_PASSWORD_DEFAULT', 'Pdpa@2025!'),
            'is_builtin' => true,
        ],
        [
            'name'       => 'DPO 02',
            'email'      => 'dpo02@pdpa.local',
            'role'       => 'dpo',
            'password'   => env('SEED_PASSWORD_DEFAULT', 'Pdpa@2025!'),
            'is_builtin' => true,
        ],
        [
            'name'       => 'DPO 03',
            'email'      => 'dpo03@pdpa.local',
            'role'       => 'dpo',
            'password'   => env('SEED_PASSWORD_DEFAULT', 'Pdpa@2025!'),
            'is_builtin' => true,
        ],

        // ── Reviewer (4) ──────────────────────────────────────────────────
        [
            'name'       => 'Reviewer 01',
            'email'      => 'reviewer01@pdpa.local',
            'role'       => 'reviewer',
            'password'   => env('SEED_PASSWORD_DEFAULT', 'Pdpa@2025!'),
            'is_builtin' => true,
        ],
        [
            'name'       => 'Reviewer 02',
            'email'      => 'reviewer02@pdpa.local',
            'role'       => 'reviewer',
            'password'   => env('SEED_PASSWORD_DEFAULT', 'Pdpa@2025!'),
            'is_builtin' => true,
        ],
        [
            'name'       => 'Reviewer 03',
            'email'      => 'reviewer03@pdpa.local',
            'role'       => 'reviewer',
            'password'   => env('SEED_PASSWORD_DEFAULT', 'Pdpa@2025!'),
            'is_builtin' => true,
        ],
        [
            'name'       => 'Reviewer 04',
            'email'      => 'reviewer04@pdpa.local',
            'role'       => 'reviewer',
            'password'   => env('SEED_PASSWORD_DEFAULT', 'Pdpa@2025!'),
            'is_builtin' => true,
        ],

    ],

];
