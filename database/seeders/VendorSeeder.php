<?php

namespace Database\Seeders;

use App\Models\Vendor;
use App\Models\VendorAssessment;
use Illuminate\Database\Seeder;

class VendorSeeder extends Seeder
{
    public function run(): void
    {
        $vendors = [
            // Org 1
            ['organization_id' => 1, 'name' => 'Mailchimp (Intuit)', 'country' => 'US', 'website' => 'https://mailchimp.com', 'contact_name' => 'Support Team', 'contact_email' => 'support@mailchimp.com', 'services_description' => 'Email Marketing Platform', 'data_types_shared' => ['ชื่อ', 'อีเมล'], 'role' => 'processor', 'risk_level' => 'medium', 'is_cross_border' => true, 'transfer_mechanism' => 'Standard Contractual Clauses (SCCs)', 'dpa_signed' => true, 'dpa_signed_at' => '2023-01-01', 'dpa_expires_at' => now()->addDays(15)->format('Y-m-d'), 'status' => 'active', 'created_by' => 2],
            ['organization_id' => 1, 'name' => 'Stripe Inc.', 'country' => 'US', 'website' => 'https://stripe.com', 'contact_name' => 'Legal Team', 'contact_email' => 'legal@stripe.com', 'services_description' => 'Payment Processing', 'data_types_shared' => ['ชื่อ', 'ข้อมูลบัตรเครดิต', 'อีเมล'], 'role' => 'processor', 'risk_level' => 'high', 'is_cross_border' => true, 'transfer_mechanism' => 'SCCs + Adequacy Decision', 'dpa_signed' => true, 'dpa_signed_at' => '2023-06-01', 'dpa_expires_at' => now()->addMonths(8)->format('Y-m-d'), 'status' => 'active', 'created_by' => 2],
            ['organization_id' => 1, 'name' => 'Kerry Express', 'country' => 'TH', 'website' => 'https://kerryexpress.com', 'contact_name' => 'ฝ่ายธุรกิจองค์กร', 'contact_email' => 'corporate@kerryexpress.com', 'services_description' => 'จัดส่งพัสดุในประเทศ', 'data_types_shared' => ['ชื่อ', 'ที่อยู่', 'เบอร์โทร'], 'role' => 'processor', 'risk_level' => 'low', 'is_cross_border' => false, 'dpa_signed' => true, 'dpa_signed_at' => '2023-03-01', 'dpa_expires_at' => now()->addMonths(6)->format('Y-m-d'), 'status' => 'active', 'created_by' => 2],
            ['organization_id' => 1, 'name' => 'Google Analytics (Google LLC)', 'country' => 'US', 'website' => 'https://analytics.google.com', 'services_description' => 'Web Analytics', 'data_types_shared' => ['IP Address', 'Cookie', 'พฤติกรรมการใช้งาน'], 'role' => 'processor', 'risk_level' => 'medium', 'is_cross_border' => true, 'transfer_mechanism' => 'SCCs', 'dpa_signed' => true, 'dpa_signed_at' => '2023-01-01', 'dpa_expires_at' => now()->subMonths(2)->format('Y-m-d'), 'status' => 'under_review', 'created_by' => 2],
            // Org 2
            ['organization_id' => 2, 'name' => 'บริษัทประกันสุขภาพไทย จำกัด', 'country' => 'TH', 'contact_name' => 'ฝ่ายประสาน', 'contact_email' => 'pdpa@thaiinsurance.example.com', 'services_description' => 'รับเรียกร้องค่าสินไหมทดแทนประกันสุขภาพ', 'data_types_shared' => ['ข้อมูลสุขภาพ', 'ประวัติการรักษา'], 'role' => 'controller', 'risk_level' => 'high', 'is_cross_border' => false, 'dpa_signed' => true, 'dpa_signed_at' => '2022-01-01', 'dpa_expires_at' => now()->addMonths(3)->format('Y-m-d'), 'status' => 'active', 'created_by' => 6],
            ['organization_id' => 2, 'name' => 'โรงพยาบาลพันธมิตร (เครือข่าย)', 'country' => 'TH', 'services_description' => 'ส่งต่อผู้ป่วยในเครือข่ายโรงพยาบาล', 'data_types_shared' => ['ข้อมูลสุขภาพ', 'ประวัติการรักษา', 'ผลตรวจ'], 'role' => 'joint_controller', 'risk_level' => 'high', 'is_cross_border' => false, 'dpa_signed' => false, 'status' => 'under_review', 'created_by' => 6],
            // Org 3
            ['organization_id' => 3, 'name' => 'NCBA (National Credit Bureau)', 'country' => 'TH', 'website' => 'https://ncb.co.th', 'services_description' => 'บริการข้อมูลเครดิต', 'data_types_shared' => ['เลขบัตรประชาชน', 'ประวัติเครดิต'], 'role' => 'controller', 'risk_level' => 'high', 'is_cross_border' => false, 'dpa_signed' => true, 'dpa_signed_at' => '2023-01-01', 'dpa_expires_at' => now()->addMonths(10)->format('Y-m-d'), 'status' => 'active', 'created_by' => 9],
            ['organization_id' => 3, 'name' => 'AWS (Amazon Web Services)', 'country' => 'SG', 'website' => 'https://aws.amazon.com', 'services_description' => 'Cloud Infrastructure (Singapore Region)', 'data_types_shared' => ['ทุกประเภท (encrypted)'], 'role' => 'processor', 'risk_level' => 'low', 'is_cross_border' => true, 'transfer_mechanism' => 'Adequacy Decision + ISO 27001', 'dpa_signed' => true, 'dpa_signed_at' => '2023-01-01', 'dpa_expires_at' => now()->addMonths(14)->format('Y-m-d'), 'status' => 'active', 'created_by' => 9],
        ];

        foreach ($vendors as $v) {
            Vendor::create($v);
        }

        // Vendor Assessments
        VendorAssessment::insert([
            ['vendor_id' => 2, 'assessed_by' => 2, 'score' => 85, 'risk_level' => 'low', 'findings' => 'PCI DSS Certified, Good security practices', 'recommendations' => 'Monitor for policy changes', 'next_assessment_date' => now()->addYear()->format('Y-m-d'), 'created_at' => now()->subMonths(3), 'updated_at' => now()->subMonths(3)],
            ['vendor_id' => 4, 'assessed_by' => 2, 'score' => 55, 'risk_level' => 'medium', 'findings' => 'DPA หมดอายุ ต้องต่ออายุ', 'recommendations' => 'ต่ออายุ DPA และพิจารณา GA4 แทน Universal Analytics', 'next_assessment_date' => now()->addMonths(3)->format('Y-m-d'), 'created_at' => now()->subMonths(1), 'updated_at' => now()->subMonths(1)],
        ]);

        $this->command->info('✅ Vendors seeded (8 vendors + assessments)');
        $this->command->warn('   ⚠️  Mailchimp DPA expires in 15 days!');
    }
}
