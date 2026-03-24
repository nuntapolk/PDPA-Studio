<?php

namespace Database\Seeders;

use App\Models\PrivacyNotice;
use Illuminate\Database\Seeder;

class PrivacyNoticeSeeder extends Seeder
{
    public function run(): void
    {
        $content = [
            'privacy_policy' => '<h2>นโยบายความเป็นส่วนตัว</h2>
<p>บริษัทให้ความสำคัญกับการคุ้มครองข้อมูลส่วนบุคคลของท่านตามพระราชบัญญัติคุ้มครองข้อมูลส่วนบุคคล พ.ศ. 2562 ("PDPA")</p>
<h3>1. ข้อมูลที่เราเก็บรวบรวม</h3>
<p>เราเก็บรวบรวมข้อมูลส่วนบุคคล ได้แก่ ชื่อ-นามสกุล ที่อยู่ อีเมล หมายเลขโทรศัพท์ และข้อมูลอื่นที่จำเป็นสำหรับการให้บริการ</p>
<h3>2. วัตถุประสงค์การใช้ข้อมูล</h3>
<p>เราใช้ข้อมูลของท่านเพื่อให้บริการ ปรับปรุงผลิตภัณฑ์ และปฏิบัติตามกฎหมายที่เกี่ยวข้อง</p>
<h3>3. ระยะเวลาการเก็บรักษา</h3>
<p>เราจะเก็บรักษาข้อมูลของท่านตลอดระยะเวลาที่จำเป็นหรือตามที่กฎหมายกำหนด</p>
<h3>4. สิทธิ์ของเจ้าของข้อมูล</h3>
<p>ท่านมีสิทธิ์เข้าถึง แก้ไข ลบ และโอนย้ายข้อมูลส่วนบุคคลของท่าน กรุณาติดต่อ DPO เพื่อใช้สิทธิ์ดังกล่าว</p>
<h3>5. ช่องทางติดต่อ</h3>
<p>เจ้าหน้าที่คุ้มครองข้อมูลส่วนบุคคล (DPO): dpo@company.th | โทร. 02-xxx-xxxx</p>',
            'cookie_policy' => '<h2>นโยบาย Cookie</h2>
<p>เว็บไซต์ของเราใช้ Cookie และเทคโนโลยีที่คล้ายกันเพื่อปรับปรุงประสบการณ์การใช้งาน</p>
<h3>Cookie คืออะไร?</h3>
<p>Cookie คือไฟล์ข้อความขนาดเล็กที่จัดเก็บในอุปกรณ์ของท่านเมื่อเยี่ยมชมเว็บไซต์</p>
<h3>ประเภท Cookie ที่เราใช้</h3>
<p><strong>Cookie จำเป็น (Necessary):</strong> ใช้สำหรับการทำงานพื้นฐานของเว็บไซต์ ไม่สามารถปิดได้</p>
<p><strong>Cookie วิเคราะห์ (Analytics):</strong> ช่วยให้เราเข้าใจวิธีที่ท่านใช้งานเว็บไซต์</p>
<p><strong>Cookie การตลาด (Marketing):</strong> ใช้สำหรับแสดงโฆษณาที่เกี่ยวข้องกับท่าน</p>
<h3>การจัดการ Cookie</h3>
<p>ท่านสามารถปิด Cookie ได้ผ่านการตั้งค่าเบราว์เซอร์ หรือคลิก "ตั้งค่า Cookie" ที่แบนเนอร์</p>',
            'employee_notice' => '<h2>ประกาศความเป็นส่วนตัวสำหรับพนักงาน</h2>
<p>บริษัทเก็บรวบรวมและใช้ข้อมูลส่วนบุคคลของพนักงานเพื่อวัตถุประสงค์ในการบริหารงานบุคคล</p>
<h3>ข้อมูลที่เก็บรวบรวม</h3>
<p>ข้อมูลส่วนตัว ข้อมูลการจ้างงาน เงินเดือน การประเมินผล ข้อมูลสุขภาพ (เฉพาะที่จำเป็น)</p>
<h3>วัตถุประสงค์</h3>
<p>การบริหารทรัพยากรบุคคล การจ่ายค่าตอบแทน การฝึกอบรม และการปฏิบัติตามกฎหมายแรงงาน</p>
<h3>สิทธิ์ของพนักงาน</h3>
<p>พนักงานสามารถขอเข้าถึง แก้ไข และลบข้อมูลส่วนตัวได้ผ่านฝ่าย HR</p>',
            'cctv_notice' => '<h2>ประกาศการใช้กล้องวงจรปิด (CCTV)</h2>
<p>บริษัทได้ติดตั้งกล้องวงจรปิดในพื้นที่ที่ระบุเพื่อวัตถุประสงค์ด้านความปลอดภัย</p>
<h3>วัตถุประสงค์</h3>
<p>เพื่อป้องกันและระงับเหตุอาชญากรรม ปกป้องทรัพย์สิน และความปลอดภัยของบุคคลในพื้นที่</p>
<h3>ระยะเวลาการเก็บภาพ</h3>
<p>ภาพวิดีโอจะถูกเก็บรักษาไว้ 30 วัน หลังจากนั้นจะถูกลบออกโดยอัตโนมัติ</p>
<h3>การเข้าถึงข้อมูล</h3>
<p>เฉพาะเจ้าหน้าที่ที่ได้รับอนุญาตเท่านั้นที่สามารถเข้าถึงภาพ CCTV</p>',
            'marketing_notice' => '<h2>ประกาศการใช้ข้อมูลเพื่อวัตถุประสงค์การตลาด</h2>
<p>บริษัทอาจใช้ข้อมูลส่วนบุคคลของท่านเพื่อการนำเสนอผลิตภัณฑ์และบริการที่ท่านอาจสนใจ</p>
<h3>ข้อมูลที่ใช้</h3>
<p>ชื่อ อีเมล เบอร์โทร ประวัติการซื้อ และพฤติกรรมการใช้บริการ</p>
<h3>การยกเลิกการรับการตลาด</h3>
<p>ท่านสามารถยกเลิกได้ทุกเมื่อโดยคลิก Unsubscribe ในอีเมล หรือติดต่อ marketing@company.th</p>',
        ];

        $notices = [
            ['organization_id'=>1,'type'=>'privacy_policy',  'language'=>'th','title'=>'นโยบายความเป็นส่วนตัว บริษัท ไทยช้อป จำกัด',  'version'=>3,'published_at'=>now()->subMonths(3),'is_active'=>true,'effective_date'=>'1 มกราคม 2568','created_by'=>2,'approved_by'=>1,'content'=>$content['privacy_policy']],
            ['organization_id'=>1,'type'=>'privacy_policy',  'language'=>'en','title'=>'Privacy Policy — ThaiShop Co., Ltd.',         'version'=>3,'published_at'=>now()->subMonths(3),'is_active'=>true,'effective_date'=>'January 1, 2025','created_by'=>2,'approved_by'=>1,'content'=>'<h2>Privacy Policy</h2><p>ThaiShop Co., Ltd. is committed to protecting your personal data in accordance with PDPA.</p><h3>Data We Collect</h3><p>We collect name, email, phone number, purchase history, and device information.</p><h3>Your Rights</h3><p>You have the right to access, rectify, erase, and port your personal data. Contact our DPO at dpo@company.th</p>'],
            ['organization_id'=>1,'type'=>'cookie_policy',   'language'=>'th','title'=>'นโยบาย Cookie บริษัท ไทยช้อป จำกัด',        'version'=>2,'published_at'=>now()->subMonths(3),'is_active'=>true,'effective_date'=>'1 มกราคม 2568','created_by'=>2,'content'=>$content['cookie_policy']],
            ['organization_id'=>1,'type'=>'employee_notice', 'language'=>'th','title'=>'ประกาศความเป็นส่วนตัวสำหรับพนักงาน',          'version'=>1,'published_at'=>now()->subMonths(3),'is_active'=>true,'effective_date'=>'1 มกราคม 2568','created_by'=>2,'content'=>$content['employee_notice']],
            ['organization_id'=>1,'type'=>'cctv_notice',     'language'=>'th','title'=>'ประกาศการใช้กล้องวงจรปิด (CCTV)',               'version'=>1,'published_at'=>now()->subMonths(2),'is_active'=>true,'effective_date'=>'1 มีนาคม 2568','created_by'=>2,'content'=>$content['cctv_notice']],
            ['organization_id'=>1,'type'=>'marketing_notice','language'=>'th','title'=>'ประกาศการใช้ข้อมูลเพื่อการตลาด',               'version'=>1,'published_at'=>null,'is_active'=>false,'effective_date'=>null,'created_by'=>2,'content'=>$content['marketing_notice']],
            ['organization_id'=>1,'type'=>'third_party_notice','language'=>'th','title'=>'ประกาศการส่งข้อมูลให้บุคคลที่สาม',           'version'=>1,'published_at'=>null,'is_active'=>false,'effective_date'=>null,'created_by'=>2,'content'=>'<h2>ประกาศการเปิดเผยข้อมูลให้บุคคลที่สาม</h2><p>บริษัทอาจเปิดเผยข้อมูลส่วนบุคคลของท่านให้แก่บุคคลที่สามในกรณีที่จำเป็น</p><h3>ผู้รับข้อมูล</h3><p>พันธมิตรทางธุรกิจ ผู้ให้บริการภายนอก และหน่วยงานกำกับดูแล</p>'],
            ['organization_id'=>1,'type'=>'privacy_policy',  'language'=>'th','title'=>'นโยบายความเป็นส่วนตัว (เวอร์ชันเก่า v2)',      'version'=>2,'published_at'=>now()->subMonths(12),'is_active'=>false,'effective_date'=>'1 มกราคม 2567','created_by'=>2,'approved_by'=>1,'content'=>'<h2>นโยบายความเป็นส่วนตัว (เวอร์ชัน 2)</h2><p>นโยบายฉบับเก่า — ถูกแทนที่ด้วยเวอร์ชัน 3 แล้ว</p>','expires_at'=>now()->subMonths(3)],
        ];

        PrivacyNotice::query()->forceDelete();
        foreach ($notices as $n) {
            if (!isset($n['public_url'])) $n['public_url'] = \Illuminate\Support\Str::random(32);
            PrivacyNotice::create($n);
        }

        $total = PrivacyNotice::count();
        $this->command->info("✅ Privacy Notices seeded ({$total} notices, all org 1)");
    }
}
