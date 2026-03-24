<?php

namespace Database\Seeders;

use App\Models\RightsRequest;
use App\Models\RightsRequestNote;
use Illuminate\Database\Seeder;

class RightsRequestSeeder extends Seeder
{
    public function run(): void
    {
        $requests = [
            // Org 1
            [
                'organization_id' => 1, 'data_subject_id' => 1,
                'ticket_number' => 'RR-2024-00001',
                'type' => 'access', 'status' => 'completed',
                'requester_name' => 'อรุณี สวยงาม', 'requester_email' => 'arunee@example.com',
                'requester_phone' => '081-234-5678', 'requester_id_number' => '1100100000001',
                'description' => 'ต้องการทราบว่าบริษัทเก็บข้อมูลอะไรของตนเองบ้าง',
                'assigned_to' => 2,
                'due_date' => now()->subDays(5),
                'response_note' => 'ดำเนินการส่งรายงานข้อมูลทั้งหมดให้แล้ว ผ่านอีเมล',
                'submitted_at' => now()->subDays(35),
                'acknowledged_at' => now()->subDays(34),
                'completed_at' => now()->subDays(5),
            ],
            [
                'organization_id' => 1, 'data_subject_id' => 4,
                'ticket_number' => 'RR-2024-00002',
                'type' => 'erasure', 'status' => 'in_review',
                'requester_name' => 'วิทยา เก่งกาจ', 'requester_email' => 'wittaya@example.com',
                'requester_phone' => '084-567-8901',
                'description' => 'ต้องการให้ลบข้อมูลทั้งหมดออกจากระบบ เนื่องจากไม่ได้ใช้บริการแล้ว',
                'assigned_to' => 2,
                'due_date' => now()->addDays(15),
                'submitted_at' => now()->subDays(15),
                'acknowledged_at' => now()->subDays(14),
            ],
            [
                'organization_id' => 1, 'data_subject_id' => 3,
                'ticket_number' => 'RR-2024-00003',
                'type' => 'rectification', 'status' => 'pending',
                'requester_name' => 'สุดารัตน์ ดีเลิศ', 'requester_email' => 'sudarat@example.com',
                'description' => 'เบอร์โทรศัพท์ในระบบไม่ถูกต้อง ต้องการแก้ไขเป็น 083-999-8888',
                'assigned_to' => 3,
                'due_date' => now()->addDays(25),
                'submitted_at' => now()->subDays(5),
            ],
            [
                'organization_id' => 1, 'data_subject_id' => 2,
                'ticket_number' => 'RR-2024-00004',
                'type' => 'portability', 'status' => 'awaiting_info',
                'requester_name' => 'ประเสริฐ มีสุข', 'requester_email' => 'prasert@example.com',
                'description' => 'ต้องการรับข้อมูลการซื้อทั้งหมดในรูปแบบ CSV เพื่อย้ายไปใช้บริการใหม่',
                'assigned_to' => 2,
                'due_date' => now()->addDays(20),
                'submitted_at' => now()->subDays(10),
                'acknowledged_at' => now()->subDays(9),
            ],
            // Org 1 — Overdue! (เกินกำหนด)
            [
                'organization_id' => 1, 'data_subject_id' => 5,
                'ticket_number' => 'RR-2024-00005',
                'type' => 'objection', 'status' => 'pending',
                'requester_name' => 'นภาพร สดใส', 'requester_email' => 'napaporn@example.com',
                'description' => 'คัดค้านการใช้ข้อมูลเพื่อวัตถุประสงค์การตลาด',
                'due_date' => now()->subDays(2),
                'submitted_at' => now()->subDays(32),
            ],
            // Org 2 — Hospital
            [
                'organization_id' => 2, 'data_subject_id' => 8,
                'ticket_number' => 'RR-2024-00006',
                'type' => 'access', 'status' => 'completed',
                'requester_name' => 'บุญมี แข็งแรง', 'requester_email' => 'boonmee@example.com',
                'description' => 'ขอประวัติการรักษาทั้งหมดในช่วง 2 ปีที่ผ่านมา',
                'assigned_to' => 6,
                'due_date' => now()->subDays(10),
                'response_note' => 'จัดส่งสำเนาเวชระเบียนให้แล้ว',
                'submitted_at' => now()->subDays(40),
                'acknowledged_at' => now()->subDays(39),
                'completed_at' => now()->subDays(10),
            ],
            // Org 3
            [
                'organization_id' => 3, 'data_subject_id' => 12,
                'ticket_number' => 'RR-2024-00007',
                'type' => 'restriction', 'status' => 'in_review',
                'requester_name' => 'ธนพล รวยดี', 'requester_email' => 'thanapol@example.com',
                'description' => 'ขอระงับการใช้ข้อมูลเพื่อ Credit Scoring ระหว่างรอการพิจารณาอุทธรณ์',
                'assigned_to' => 9,
                'due_date' => now()->addDays(18),
                'submitted_at' => now()->subDays(12),
                'acknowledged_at' => now()->subDays(11),
            ],
        ];

        foreach ($requests as $r) {
            RightsRequest::create($r);
        }

        // Add notes/timeline to some requests
        RightsRequestNote::insert([
            ['rights_request_id' => 1, 'user_id' => 2, 'note' => 'รับทราบคำร้อง กำลังรวบรวมข้อมูล', 'is_internal' => true, 'created_at' => now()->subDays(34), 'updated_at' => now()->subDays(34)],
            ['rights_request_id' => 1, 'user_id' => 2, 'note' => 'ส่งรายงานข้อมูลให้ผู้ร้องขอทางอีเมลเรียบร้อยแล้ว', 'is_internal' => false, 'created_at' => now()->subDays(5), 'updated_at' => now()->subDays(5)],
            ['rights_request_id' => 2, 'user_id' => 2, 'note' => 'ตรวจสอบแล้วพบว่ามีข้อมูลในหลายระบบ กำลังดำเนินการลบ', 'is_internal' => true, 'created_at' => now()->subDays(12), 'updated_at' => now()->subDays(12)],
            ['rights_request_id' => 4, 'user_id' => 2, 'note' => 'รอยืนยันตัวตนจากผู้ร้องขอ กรุณาส่งสำเนาบัตรประชาชน', 'is_internal' => false, 'created_at' => now()->subDays(8), 'updated_at' => now()->subDays(8)],
        ]);

        $this->command->info('✅ Rights Requests seeded (7 requests with timeline notes)');
    }
}
