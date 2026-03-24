<?php

namespace Database\Seeders;

use App\Models\DataSubject;
use Illuminate\Database\Seeder;

class DataSubjectSeeder extends Seeder
{
    public function run(): void
    {
        $subjects = [
            // Org 1 — E-Commerce customers
            ['organization_id' => 1, 'type' => 'customer', 'reference_id' => 'CUST-001', 'first_name' => 'อรุณี', 'last_name' => 'สวยงาม', 'email' => 'arunee@example.com', 'phone' => '081-234-5678', 'national_id' => '1100100000001'],
            ['organization_id' => 1, 'type' => 'customer', 'reference_id' => 'CUST-002', 'first_name' => 'ประเสริฐ', 'last_name' => 'มีสุข', 'email' => 'prasert@example.com', 'phone' => '082-345-6789', 'national_id' => '1100100000002'],
            ['organization_id' => 1, 'type' => 'customer', 'reference_id' => 'CUST-003', 'first_name' => 'สุดารัตน์', 'last_name' => 'ดีเลิศ', 'email' => 'sudarat@example.com', 'phone' => '083-456-7890'],
            ['organization_id' => 1, 'type' => 'customer', 'reference_id' => 'CUST-004', 'first_name' => 'วิทยา', 'last_name' => 'เก่งกาจ', 'email' => 'wittaya@example.com', 'phone' => '084-567-8901'],
            ['organization_id' => 1, 'type' => 'customer', 'reference_id' => 'CUST-005', 'first_name' => 'นภาพร', 'last_name' => 'สดใส', 'email' => 'napaporn@example.com', 'phone' => '085-678-9012'],
            ['organization_id' => 1, 'type' => 'employee', 'reference_id' => 'EMP-001', 'first_name' => 'สมศักดิ์', 'last_name' => 'ทำงานดี', 'email' => 'somsak@thaishop-staff.com', 'phone' => '086-789-0123'],
            ['organization_id' => 1, 'type' => 'employee', 'reference_id' => 'EMP-002', 'first_name' => 'กนกวรรณ', 'last_name' => 'ขยันหมั่น', 'email' => 'kanok@thaishop-staff.com', 'phone' => '087-890-1234'],
            // Org 2 — Hospital patients
            ['organization_id' => 2, 'type' => 'patient', 'reference_id' => 'HN-00001', 'first_name' => 'บุญมี', 'last_name' => 'แข็งแรง', 'email' => 'boonmee@example.com', 'phone' => '088-901-2345', 'date_of_birth' => '1975-06-15'],
            ['organization_id' => 2, 'type' => 'patient', 'reference_id' => 'HN-00002', 'first_name' => 'ศรีสุดา', 'last_name' => 'หายป่วย', 'email' => 'srisuda@example.com', 'phone' => '089-012-3456', 'date_of_birth' => '1988-03-22'],
            ['organization_id' => 2, 'type' => 'patient', 'reference_id' => 'HN-00003', 'first_name' => 'ธงชัย', 'last_name' => 'สบายดี', 'email' => 'thongchai@example.com', 'phone' => '090-123-4567', 'date_of_birth' => '1965-11-08'],
            ['organization_id' => 2, 'type' => 'employee', 'reference_id' => 'DR-001', 'first_name' => 'แพทย์หญิง อรอุมา', 'last_name' => 'รักษาดี', 'email' => 'doctor@muangthai-hospital.example.com', 'phone' => '091-234-5678'],
            // Org 3 — Fintech customers
            ['organization_id' => 3, 'type' => 'customer', 'reference_id' => 'FIN-001', 'first_name' => 'ธนพล', 'last_name' => 'รวยดี', 'email' => 'thanapol@example.com', 'phone' => '092-345-6789'],
            ['organization_id' => 3, 'type' => 'customer', 'reference_id' => 'FIN-002', 'first_name' => 'อาภาพร', 'last_name' => 'ออมทรัพย์', 'email' => 'apaporn@example.com', 'phone' => '093-456-7890'],
            ['organization_id' => 3, 'type' => 'customer', 'reference_id' => 'FIN-003', 'first_name' => 'ณัฐพล', 'last_name' => 'ลงทุนดี', 'email' => 'nattapon@example.com', 'phone' => '094-567-8901'],
        ];

        foreach ($subjects as $subject) {
            DataSubject::create(array_merge($subject, ['status' => 'active']));
        }

        $this->command->info('✅ Data Subjects seeded (14 subjects)');
    }
}
