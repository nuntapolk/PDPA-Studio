<?php

namespace Database\Seeders;

use App\Models\BreachIncident;
use App\Models\BreachTimeline;
use Illuminate\Database\Seeder;

class BreachSeeder extends Seeder
{
    public function run(): void
    {
        $incidents = [
            // Org 1 — Resolved breach
            [
                'organization_id' => 1, 'incident_number' => 'BR-2024-00001',
                'title' => 'ข้อมูลลูกค้าถูก Phishing',
                'description' => 'พนักงานฝ่ายขายถูกหลอกให้กรอกข้อมูล credentials ในเว็บปลอม ทำให้ผู้ไม่ประสงค์ดีเข้าถึงระบบ CRM และดูข้อมูลลูกค้าได้',
                'breach_type' => 'unauthorized_access',
                'severity' => 'high',
                'status' => 'resolved',
                'discovered_at' => now()->subDays(45),
                'occurred_at' => now()->subDays(46),
                'affected_count' => 150,
                'data_types_affected' => ['ชื่อ-นามสกุล', 'อีเมล', 'เบอร์โทรศัพท์', 'ที่อยู่'],
                'includes_sensitive_data' => false,
                'impact_assessment' => 'ข้อมูลส่วนบุคคลพื้นฐานถูกเปิดเผย ไม่มีข้อมูลการเงิน',
                'requires_pdpc_notification' => true,
                'pdpc_notification_deadline' => now()->subDays(43),
                'pdpc_notified_at' => now()->subDays(43),
                'pdpc_reference_number' => 'PDPC-2024-BR-0123',
                'requires_subject_notification' => true,
                'subjects_notified_at' => now()->subDays(40),
                'containment_actions' => 'รีเซ็ต passwords ทั้งหมด เพิ่ม MFA บังคับ ปิด session เก่า',
                'root_cause' => 'พนักงานขาดความตระหนักเรื่อง Phishing',
                'corrective_actions' => 'อบรม Security Awareness ทีมงาน เพิ่ม Email Filtering',
                'preventive_measures' => 'บังคับ MFA ทุก account อบรมรายปี',
                'reported_by' => 3,
                'assigned_to' => 2,
                'resolved_at' => now()->subDays(35),
            ],
            // Org 1 — Active breach (urgent!)
            [
                'organization_id' => 1, 'incident_number' => 'BR-2024-00002',
                'title' => 'Database Backup ถูก Expose',
                'description' => 'พบว่า S3 bucket ที่เก็บ Database backup ถูกตั้งค่าเป็น Public โดยไม่ตั้งใจ อาจถูก Access จากภายนอก',
                'breach_type' => 'system_vulnerability',
                'severity' => 'critical',
                'status' => 'investigating',
                'discovered_at' => now()->subHours(30),
                'affected_count' => 5000,
                'data_types_affected' => ['ชื่อ-นามสกุล', 'อีเมล', 'เบอร์โทร', 'ที่อยู่', 'ประวัติการซื้อ'],
                'includes_sensitive_data' => false,
                'impact_assessment' => 'กำลังประเมิน ยังไม่ทราบว่ามีการ Download ข้อมูลหรือไม่',
                'requires_pdpc_notification' => true,
                'pdpc_notification_deadline' => now()->addHours(42),
                'requires_subject_notification' => false,
                'containment_actions' => 'ปิด Public access ของ S3 bucket แล้ว กำลังตรวจสอบ Access Log',
                'reported_by' => 3,
                'assigned_to' => 2,
            ],
            // Org 2 — Hospital (Sensitive data!)
            [
                'organization_id' => 2, 'incident_number' => 'BR-2024-00003',
                'title' => 'Laptop พนักงานหาย',
                'description' => 'Laptop ของเจ้าหน้าที่ฝ่ายเวชระเบียนสูญหาย อาจมีไฟล์ผู้ป่วยที่ยังไม่ได้เข้ารหัส',
                'breach_type' => 'physical_breach',
                'severity' => 'high',
                'status' => 'contained',
                'discovered_at' => now()->subDays(5),
                'affected_count' => 45,
                'data_types_affected' => ['ข้อมูลสุขภาพ', 'ชื่อ-นามสกุล', 'HN'],
                'includes_sensitive_data' => true,
                'requires_pdpc_notification' => true,
                'pdpc_notification_deadline' => now()->subDays(2),
                'pdpc_notified_at' => now()->subDays(2),
                'pdpc_reference_number' => 'PDPC-2024-BR-0456',
                'requires_subject_notification' => true,
                'containment_actions' => 'Remote wipe Laptop แล้ว เปลี่ยน password ระบบ',
                'root_cause' => 'ไม่มีนโยบาย Full Disk Encryption',
                'reported_by' => 7,
                'assigned_to' => 6,
            ],
            // Org 3 — Resolved
            [
                'organization_id' => 3, 'incident_number' => 'BR-2024-00004',
                'title' => 'SQL Injection Attack',
                'description' => 'พบการโจมตี SQL Injection บนฟอร์มค้นหา แต่ถูก WAF บล็อกก่อนที่จะเข้าถึงข้อมูล',
                'breach_type' => 'system_vulnerability',
                'severity' => 'medium',
                'status' => 'closed',
                'discovered_at' => now()->subDays(60),
                'affected_count' => 0,
                'data_types_affected' => [],
                'includes_sensitive_data' => false,
                'requires_pdpc_notification' => false,
                'requires_subject_notification' => false,
                'containment_actions' => 'WAF Rule อัปเดต Patch ระบบ',
                'root_cause' => 'Input validation ไม่ครบถ้วน',
                'corrective_actions' => 'Code review เพิ่ม Parameterized Query ทั้งหมด',
                'reported_by' => 8,
                'assigned_to' => 9,
                'resolved_at' => now()->subDays(55),
            ],
        ];

        foreach ($incidents as $i) {
            BreachIncident::create($i);
        }

        // Timeline
        BreachTimeline::insert([
            ['breach_incident_id' => 1, 'user_id' => 3, 'action' => 'รายงานเหตุการณ์', 'description' => 'พบกิจกรรมผิดปกติใน CRM ลูกค้าร้องเรียนว่ามีคนโทรหาโดยใช้ข้อมูลส่วนตัว', 'created_at' => now()->subDays(45), 'updated_at' => now()->subDays(45)],
            ['breach_incident_id' => 1, 'user_id' => 2, 'action' => 'ระงับเหตุ', 'description' => 'รีเซ็ต password พนักงานทั้งหมด เพิ่ม MFA', 'created_at' => now()->subDays(44), 'updated_at' => now()->subDays(44)],
            ['breach_incident_id' => 1, 'user_id' => 2, 'action' => 'แจ้ง PDPC', 'description' => 'ส่งแบบฟอร์มแจ้งเหตุละเมิดต่อ สคส. ได้รับเลข PDPC-2024-BR-0123', 'created_at' => now()->subDays(43), 'updated_at' => now()->subDays(43)],
            ['breach_incident_id' => 2, 'user_id' => 3, 'action' => 'ค้นพบเหตุการณ์', 'description' => 'AWS Security Hub แจ้งเตือน S3 bucket เป็น Public', 'created_at' => now()->subHours(30), 'updated_at' => now()->subHours(30)],
            ['breach_incident_id' => 2, 'user_id' => 2, 'action' => 'ระงับเหตุ', 'description' => 'ปิด Public access ทันที กำลังตรวจสอบ Access Log 30 วันย้อนหลัง', 'created_at' => now()->subHours(28), 'updated_at' => now()->subHours(28)],
        ]);

        $this->command->info('✅ Breach Incidents seeded (4 incidents)');
        $this->command->warn('   ⚠️  BR-2024-00002: CRITICAL breach — 42 hours until PDPC deadline!');
    }
}
