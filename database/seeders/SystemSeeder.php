<?php

namespace Database\Seeders;

use App\Models\ApiKey;
use App\Models\AuditLog;
use App\Models\Webhook;
use Illuminate\Database\Seeder;

class SystemSeeder extends Seeder
{
    public function run(): void
    {
        // API Keys
        $rawKey1 = 'psk_demo_key_org1_' . str_repeat('a', 30);
        $rawKey2 = 'psk_demo_key_org2_' . str_repeat('b', 30);

        ApiKey::insert([
            [
                'organization_id' => 1,
                'name' => 'HR System Integration',
                'key_prefix' => 'psk_demo',
                'key_hash' => bcrypt($rawKey1),
                'permissions' => json_encode(['consents:read', 'consents:write', 'subjects:read', 'subjects:write']),
                'allowed_ips' => '192.168.1.0/24',
                'is_active' => true,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'organization_id' => 1,
                'name' => 'Website Cookie Banner',
                'key_prefix' => 'psk_cook',
                'allowed_ips' => null,
                'key_hash' => bcrypt('psk_cookie_banner_key_xxxxxxxxxxxxxxxxxxxxxx'),
                'permissions' => json_encode(['cookie_consents:write']),
                'is_active' => true,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'organization_id' => 3,
                'name' => 'Core Banking Integration',
                'key_prefix' => 'psk_bank',
                'key_hash' => bcrypt($rawKey2),
                'permissions' => json_encode(['*']),
                'allowed_ips' => null,
                'is_active' => true,
                'created_by' => 8,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Webhooks
        Webhook::insert([
            [
                'organization_id' => 1,
                'name' => 'HR System — Consent Update',
                'url' => 'https://hr-system.thaishop.internal/api/pdpa/consent-webhook',
                'secret' => bin2hex(random_bytes(32)),
                'events' => json_encode(['consent.granted', 'consent.withdrawn']),
                'headers' => json_encode(['X-Source' => 'PDPA-Studio']),
                'is_active' => true,
                'success_count' => 42,
                'failure_count' => 1,
                'last_triggered_at' => now()->subHours(2),
                'last_error' => null,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'organization_id' => 1,
                'name' => 'Slack — Breach Alert',
                'url' => 'https://hooks.slack.com/services/DEMO/WEBHOOK/URL',
                'secret' => bin2hex(random_bytes(32)),
                'events' => json_encode(['breach.reported', 'breach.72hr.approaching']),
                'headers' => null,
                'is_active' => true,
                'success_count' => 3,
                'failure_count' => 0,
                'last_triggered_at' => null,
                'last_error' => null,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'organization_id' => 2,
                'name' => 'HIS — Rights Request',
                'url' => 'https://his.muangthai-hospital.internal/api/pdpa',
                'secret' => bin2hex(random_bytes(32)),
                'events' => json_encode(['rights.request.received', 'rights.request.completed']),
                'headers' => null,
                'is_active' => true,
                'success_count' => 8,
                'failure_count' => 2,
                'last_triggered_at' => null,
                'last_error' => 'Connection timeout',
                'created_by' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        // Sample Audit Logs
        $logs = [
            ['organization_id' => 1, 'user_id' => 1, 'user_name' => 'สมชาย รักษ์ข้อมูล', 'action' => 'login', 'module' => 'auth', 'ip_address' => '203.150.10.1', 'created_at' => now()->subHours(2)],
            ['organization_id' => 1, 'user_id' => 2, 'user_name' => 'มาลีรัตน์ ปกป้องสิทธิ์', 'action' => 'created', 'module' => 'consent', 'entity_type' => 'ConsentTemplate', 'entity_id' => 1, 'entity_name' => 'ความยินยอมการตลาด', 'ip_address' => '203.150.10.2', 'created_at' => now()->subDays(3)],
            ['organization_id' => 1, 'user_id' => 2, 'user_name' => 'มาลีรัตน์ ปกป้องสิทธิ์', 'action' => 'created', 'module' => 'breach', 'entity_type' => 'BreachIncident', 'entity_id' => 2, 'entity_name' => 'Database Backup ถูก Expose', 'ip_address' => '203.150.10.2', 'created_at' => now()->subHours(29)],
            ['organization_id' => 1, 'user_id' => 3, 'user_name' => 'วิชัย ดูแลระบบ', 'action' => 'viewed', 'module' => 'rights', 'entity_type' => 'RightsRequest', 'entity_id' => 2, 'entity_name' => 'RR-2024-00002', 'ip_address' => '203.150.10.3', 'created_at' => now()->subHours(5)],
            ['organization_id' => 1, 'user_id' => 1, 'user_name' => 'สมชาย รักษ์ข้อมูล', 'action' => 'exported', 'module' => 'ropa', 'entity_name' => 'ROPA Report 2024', 'ip_address' => '203.150.10.1', 'created_at' => now()->subDays(1)],
        ];

        foreach ($logs as $log) {
            AuditLog::create(array_merge($log, ['method' => 'POST', 'url' => 'https://pdpa-studio.example.com/api/v1']));
        }

        $this->command->info('✅ System seeded (API Keys, Webhooks, Audit Logs)');
        $this->command->line('');
        $this->command->info('═══════════════════════════════════════════════════');
        $this->command->info('  PDPA Studio — Demo Data Ready!');
        $this->command->info('═══════════════════════════════════════════════════');
        $this->command->line('  🌐 Login URL  : http://localhost:8000/login');
        $this->command->line('  📧 Email      : admin@thaishop.example.com');
        $this->command->line('  🔑 Password   : Password@123');
        $this->command->line('');
        $this->command->line('  Org 2 (Hospital):');
        $this->command->line('  📧 Email      : admin@muangthai-hospital.example.com');
        $this->command->line('  📧 Email      : admin@fintechsolution.example.com');
        $this->command->line('');
        $this->command->warn('  ⚠️  BR-2024-00002: Critical breach — 42hr deadline!');
        $this->command->warn('  ⚠️  RR-2024-00005: Overdue rights request!');
        $this->command->warn('  ⚠️  Mailchimp DPA expires in 15 days!');
        $this->command->info('═══════════════════════════════════════════════════');
    }
}
