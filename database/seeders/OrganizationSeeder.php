<?php

namespace Database\Seeders;

use App\Models\Organization;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class OrganizationSeeder extends Seeder
{
    public function run(): void
    {
        $organizations = [
            [
                'name' => 'บริษัท ไทยช้อป จำกัด',
                'slug' => 'thai-shop',
                'tax_id' => '0105563012345',
                'industry' => 'e_commerce',
                'address' => '123 ถนนสุขุมวิท แขวงคลองเตย เขตคลองเตย กรุงเทพมหานคร 10110',
                'website' => 'https://thaishop.example.com',
                'dpo_name' => 'นายสมชาย รักษ์ข้อมูล',
                'dpo_email' => 'dpo@thaishop.example.com',
                'dpo_phone' => '02-123-4567',
                'plan' => 'pro',
                'status' => 'active',
                'max_users' => 50,
            ],
            [
                'name' => 'โรงพยาบาลเมืองไทย',
                'slug' => 'muangthai-hospital',
                'tax_id' => '0105563098765',
                'industry' => 'healthcare',
                'address' => '456 ถนนพระราม 9 แขวงห้วยขวาง เขตห้วยขวาง กรุงเทพมหานคร 10310',
                'website' => 'https://muangthai-hospital.example.com',
                'dpo_name' => 'นางสาวมาลี ปกป้องสิทธิ์',
                'dpo_email' => 'dpo@muangthai-hospital.example.com',
                'dpo_phone' => '02-987-6543',
                'plan' => 'enterprise',
                'status' => 'active',
                'max_users' => 200,
            ],
            [
                'name' => 'บริษัท ฟินเทค โซลูชัน จำกัด',
                'slug' => 'fintech-solution',
                'tax_id' => '0105563055555',
                'industry' => 'fintech',
                'address' => '789 อาคารสาทร เขตบางรัก กรุงเทพมหานคร 10500',
                'website' => 'https://fintechsolution.example.com',
                'dpo_name' => 'นายวิชัย การเงินดี',
                'dpo_email' => 'dpo@fintechsolution.example.com',
                'dpo_phone' => '02-555-7890',
                'plan' => 'enterprise',
                'status' => 'active',
                'max_users' => 100,
            ],
        ];

        foreach ($organizations as $org) {
            Organization::create($org);
        }

        $this->command->info('✅ Organizations seeded (3 organizations)');
    }
}
