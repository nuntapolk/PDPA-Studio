<?php

namespace Database\Seeders;

use App\Models\Consent;
use App\Models\ConsentTemplate;
use Illuminate\Database\Seeder;

class ConsentSeeder extends Seeder
{
    public function run(): void
    {
        // ── Consent Templates ──────────────────────────────
        $templates = [
            // Org 1 — E-Commerce
            [
                'organization_id' => 1, 'name' => 'ความยินยอมการตลาด', 'slug' => 'marketing-consent-org1',
                'version' => 2, 'purpose' => 'เพื่อส่งข้อมูลโปรโมชัน ข่าวสาร และข้อเสนอพิเศษผ่านช่องทางต่างๆ',
                'legal_basis' => 'consent', 'retention_days' => 365,
                'data_categories' => 'ชื่อ อีเมล เบอร์โทรศัพท์ ประวัติการซื้อ',
                'is_sensitive' => false, 'requires_explicit_consent' => false,
                'withdrawal_info' => 'สามารถถอนความยินยอมได้ตลอดเวลาผ่านการตั้งค่าบัญชีหรือคลิก Unsubscribe',
                'is_active' => true, 'created_by' => 1,
            ],
            [
                'organization_id' => 1, 'name' => 'ความยินยอมวิเคราะห์พฤติกรรม', 'slug' => 'analytics-consent-org1',
                'version' => 1, 'purpose' => 'เพื่อวิเคราะห์พฤติกรรมการใช้งานและปรับปรุงประสบการณ์ผู้ใช้',
                'legal_basis' => 'consent', 'retention_days' => 730,
                'data_categories' => 'Cookie, IP Address, พฤติกรรมการคลิก, ประวัติการดูสินค้า',
                'is_sensitive' => false, 'requires_explicit_consent' => false,
                'is_active' => true, 'created_by' => 1,
            ],
            [
                'organization_id' => 1, 'name' => 'ข้อมูลพนักงาน (สัญญาจ้าง)', 'slug' => 'employee-contract-org1',
                'version' => 1, 'purpose' => 'ประมวลผลข้อมูลพนักงานตามสัญญาจ้างงาน',
                'legal_basis' => 'contract', 'retention_days' => 3650,
                'data_categories' => 'ชื่อ ที่อยู่ เลขบัตรประชาชน บัญชีธนาคาร ประวัติการทำงาน',
                'is_sensitive' => false, 'requires_explicit_consent' => false,
                'is_active' => true, 'created_by' => 1,
            ],
            // Org 2 — Hospital
            [
                'organization_id' => 2, 'name' => 'ความยินยอมการรักษาพยาบาล', 'slug' => 'medical-consent-org2',
                'version' => 3, 'purpose' => 'เพื่อวินิจฉัยและรักษาโรค รวมถึงติดตามผลการรักษา',
                'legal_basis' => 'consent', 'retention_days' => 3650,
                'data_categories' => 'ข้อมูลสุขภาพ ประวัติการรักษา ผลตรวจ ยาที่ได้รับ',
                'is_sensitive' => true, 'requires_explicit_consent' => true,
                'withdrawal_info' => 'ผู้ป่วยสามารถถอนความยินยอมได้โดยแจ้งงานทะเบียนผู้ป่วย',
                'is_active' => true, 'created_by' => 5,
            ],
            [
                'organization_id' => 2, 'name' => 'ความยินยอมส่งต่อข้อมูลแพทย์', 'slug' => 'medical-sharing-org2',
                'version' => 1, 'purpose' => 'เพื่อส่งต่อข้อมูลให้แพทย์ผู้เชี่ยวชาญและโรงพยาบาลอื่น',
                'legal_basis' => 'consent', 'retention_days' => 1825,
                'data_categories' => 'ข้อมูลสุขภาพ ผลตรวจ รูปถ่าย',
                'is_sensitive' => true, 'requires_explicit_consent' => true,
                'is_active' => true, 'created_by' => 5,
            ],
            // Org 3 — Fintech
            [
                'organization_id' => 3, 'name' => 'ความยินยอม Credit Scoring', 'slug' => 'credit-scoring-org3',
                'version' => 2, 'purpose' => 'เพื่อประเมินความสามารถในการชำระหนี้และอนุมัติสินเชื่อ',
                'legal_basis' => 'consent', 'retention_days' => 1825,
                'data_categories' => 'ข้อมูลรายได้ ประวัติการเงิน บัญชีธนาคาร เลขบัตรประชาชน',
                'is_sensitive' => false, 'requires_explicit_consent' => true,
                'is_active' => true, 'created_by' => 8,
            ],
            [
                'organization_id' => 3, 'name' => 'ความยินยอมการตลาด Fintech', 'slug' => 'marketing-consent-org3',
                'version' => 1, 'purpose' => 'เพื่อนำเสนอผลิตภัณฑ์ทางการเงินที่เหมาะสม',
                'legal_basis' => 'consent', 'retention_days' => 365,
                'data_categories' => 'ชื่อ อีเมล เบอร์โทร พฤติกรรมการใช้แอป',
                'is_sensitive' => false, 'requires_explicit_consent' => false,
                'is_active' => true, 'created_by' => 8,
            ],
        ];

        foreach ($templates as $t) {
            ConsentTemplate::create($t);
        }

        // ── Consents (บันทึกการให้ความยินยอม) ──────────────
        $consents = [
            // Org 1 Marketing Consent (template 1)
            ['organization_id' => 1, 'data_subject_id' => 1, 'template_id' => 1, 'template_version' => 2, 'channel' => 'web', 'granted' => true, 'ip_address' => '203.150.10.1', 'granted_at' => now()->subDays(60), 'expires_at' => now()->addDays(305)],
            ['organization_id' => 1, 'data_subject_id' => 2, 'template_id' => 1, 'template_version' => 2, 'channel' => 'web', 'granted' => true, 'ip_address' => '203.150.10.2', 'granted_at' => now()->subDays(45), 'expires_at' => now()->addDays(320)],
            ['organization_id' => 1, 'data_subject_id' => 3, 'template_id' => 1, 'template_version' => 2, 'channel' => 'mobile', 'granted' => true, 'ip_address' => '203.150.10.3', 'granted_at' => now()->subDays(30), 'expires_at' => now()->addDays(335)],
            ['organization_id' => 1, 'data_subject_id' => 4, 'template_id' => 1, 'template_version' => 2, 'channel' => 'web', 'granted' => true, 'ip_address' => '203.150.10.4', 'granted_at' => now()->subDays(90), 'withdrawn_at' => now()->subDays(10), 'withdrawal_reason' => 'ไม่ต้องการรับข้อมูลการตลาดแล้ว'],
            ['organization_id' => 1, 'data_subject_id' => 5, 'template_id' => 1, 'template_version' => 2, 'channel' => 'email', 'granted' => true, 'ip_address' => '203.150.10.5', 'granted_at' => now()->subDays(15), 'expires_at' => now()->addDays(350)],
            // Org 1 Analytics (template 2)
            ['organization_id' => 1, 'data_subject_id' => 1, 'template_id' => 2, 'template_version' => 1, 'channel' => 'web', 'granted' => true, 'ip_address' => '203.150.10.1', 'granted_at' => now()->subDays(60)],
            ['organization_id' => 1, 'data_subject_id' => 2, 'template_id' => 2, 'template_version' => 1, 'channel' => 'web', 'granted' => false, 'ip_address' => '203.150.10.2', 'granted_at' => now()->subDays(45)],
            // Org 2 Medical (template 4)
            ['organization_id' => 2, 'data_subject_id' => 8, 'template_id' => 4, 'template_version' => 3, 'channel' => 'paper', 'granted' => true, 'granted_at' => now()->subDays(120)],
            ['organization_id' => 2, 'data_subject_id' => 9, 'template_id' => 4, 'template_version' => 3, 'channel' => 'paper', 'granted' => true, 'granted_at' => now()->subDays(30)],
            ['organization_id' => 2, 'data_subject_id' => 10, 'template_id' => 4, 'template_version' => 3, 'channel' => 'paper', 'granted' => true, 'granted_at' => now()->subDays(7)],
            // Org 3 Credit Scoring (template 6)
            ['organization_id' => 3, 'data_subject_id' => 12, 'template_id' => 6, 'template_version' => 2, 'channel' => 'web', 'granted' => true, 'ip_address' => '203.150.20.1', 'granted_at' => now()->subDays(14)],
            ['organization_id' => 3, 'data_subject_id' => 13, 'template_id' => 6, 'template_version' => 2, 'channel' => 'mobile', 'granted' => true, 'ip_address' => '203.150.20.2', 'granted_at' => now()->subDays(7)],
            ['organization_id' => 3, 'data_subject_id' => 14, 'template_id' => 7, 'template_version' => 1, 'channel' => 'web', 'granted' => true, 'ip_address' => '203.150.20.3', 'granted_at' => now()->subDays(3)],
        ];

        foreach ($consents as $c) {
            Consent::create($c);
        }

        $this->command->info('✅ Consent seeded (7 templates, 13 consent records)');
    }
}
