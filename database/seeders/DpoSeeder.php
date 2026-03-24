<?php

namespace Database\Seeders;

use App\Models\ComplianceChecklist;
use App\Models\DpoTask;
use Illuminate\Database\Seeder;

class DpoSeeder extends Seeder
{
    public function run(): void
    {
        DpoTask::query()->forceDelete();
        ComplianceChecklist::where('organization_id', 1)->delete();

        // ── DPO Tasks (30 tasks, org 1) ─────────────────────────────────
        $tasks = [
            // Overdue / urgent
            ['ทบทวน ROPA ประจำไตรมาส Q1/2568','ตรวจสอบและอัปเดต ROPA ให้ครบ 100% ก่อนสิ้นไตรมาส','compliance_review','urgent','in_progress','-10 days'],
            ['จัดทำ DPA กับ Vendor Cloud Storage','ทำสัญญา Data Processing Agreement กับ AWS/Azure','vendor_review','urgent','pending','-5 days'],
            ['อัปเดต Privacy Policy ล่าสุด','ปรับ Privacy Policy ให้สอดคล้องกับการเปลี่ยนแปลงระบบใหม่','policy_update','high','in_progress','-2 days'],

            // Due this week
            ['จัดอบรม PDPA พนักงานใหม่ รุ่นที่ 3','อบรมพนักงานที่เข้ามาใหม่ในไตรมาสนี้จำนวน 25 คน','training','high','pending','+3 days'],
            ['ตรวจสอบระบบ Access Control','Audit สิทธิ์การเข้าถึงระบบ HR และ Finance','audit','high','in_progress','+5 days'],
            ['รายงาน PDPA Compliance ประจำเดือน','จัดทำรายงานสรุปสถานะ Compliance ส่งผู้บริหาร','reporting','medium','pending','+7 days'],

            // Normal tasks
            ['ทบทวน Consent Template ทุกฉบับ','ตรวจสอบ Consent Form ทุก Version ให้เป็นปัจจุบัน','compliance_review','medium','pending','+14 days'],
            ['จัดทำแผน Incident Response','เตรียมแผนรับมือ Data Breach ฉบับปรับปรุง','incident_response','high','in_progress','+14 days'],
            ['อัปเดต Cookie Policy','เพิ่ม Cookie ประเภทใหม่จาก Third-party Analytics','policy_update','medium','pending','+21 days'],
            ['ทบทวน Vendor List ทั้งหมด','ตรวจสอบ Vendor ที่รับ/ส่งข้อมูลส่วนบุคคลทุกราย','vendor_review','medium','pending','+21 days'],
            ['จัดอบรม PDPA สำหรับ IT Team','อบรมเชิงลึกด้าน Technical Security สำหรับทีม IT','training','medium','pending','+30 days'],
            ['ทำ DPIA สำหรับระบบ AI ใหม่','ประเมินผลกระทบก่อน Launch ระบบ AI Recommendation','compliance_review','high','pending','+30 days'],
            ['ปรับปรุงขั้นตอน Data Subject Rights','อัปเดต SOP การรับและดำเนินการ Data Subject Request','policy_update','medium','pending','+45 days'],
            ['ตรวจสอบ Log การเข้าถึงข้อมูล','Review Audit Log ระบบ HIS และ CRM ย้อนหลัง 3 เดือน','audit','medium','pending','+45 days'],
            ['จัดทำ Employee Privacy Notice','อัปเดตประกาศความเป็นส่วนตัวสำหรับพนักงานใหม่','policy_update','low','pending','+60 days'],
            ['รายงาน Data Breach ประจำปี','สรุปเหตุการณ์ Data Breach ทั้งหมดในปีนี้','reporting','low','pending','+90 days'],

            // Completed tasks
            ['อบรม PDPA ทีม Marketing Q4/2567','อบรมพนักงานฝ่าย Marketing 15 คน','training','medium','completed',null,'-30 days'],
            ['จัดทำ DPA กับ Google Analytics','ทำ DPA ฉบับใหม่ให้สอดคล้อง GA4','vendor_review','medium','completed',null,'-20 days'],
            ['อัปเดต Privacy Policy v3','ปรับปรุงนโยบายรอบปีตามกฎหมายใหม่','policy_update','high','completed',null,'-15 days'],
            ['ตรวจสอบ CCTV ทุกจุด','ตรวจสอบป้ายแจ้งเตือน CCTV ทุกสาขา 5 แห่ง','audit','medium','completed',null,'-10 days'],
            ['ส่งรายงานประจำเดือน ก.พ. 2568','รายงาน Compliance Status ประจำเดือน','reporting','low','completed',null,'-5 days'],

            // Low priority future
            ['จัดทำ Training Manual PDPA','พัฒนาเอกสาร Training Material สำหรับพนักงานใหม่','training','low','pending','+120 days'],
            ['ทบทวนนโยบาย Cross-border Transfer','ทบทวนกระบวนการส่งข้อมูลต่างประเทศ','compliance_review','low','pending','+90 days'],
            ['Audit ระบบ Email Marketing','ตรวจสอบ Consent และ Unsubscribe Flow ระบบ Email','audit','low','pending','+75 days'],
            ['จัดทำ Privacy Handbook','คู่มือปฏิบัติงาน PDPA สำหรับพนักงานทุกระดับ','policy_update','low','pending','+120 days'],
            ['ทบทวน Retention Policy ทุกระบบ','ตรวจสอบนโยบายการเก็บและลบข้อมูลทุกระบบ','compliance_review','medium','pending','+60 days'],
            ['ติดตาม DSR Request เกินกำหนด','ตรวจสอบคำขอที่ค้างเกิน 30 วัน','incident_response','high','pending','+2 days'],
            ['รายงานสรุปรายไตรมาส Q1','สรุปผล PDPA Compliance ไตรมาสแรก','reporting','medium','pending','+15 days'],
            ['จัดทำ Risk Register PDPA','บันทึกความเสี่ยงด้าน PDPA ทั้งหมด','compliance_review','medium','in_progress','+30 days'],
            ['ประชุม DPO Committee ประจำเดือน','ประชุมคณะกรรมการ DPO รายงานความคืบหน้า','reporting','medium','pending','+8 days'],
        ];

        foreach ($tasks as $t) {
            $data = [
                'organization_id' => 1,
                'title'           => $t[0],
                'description'     => $t[1],
                'category'        => $t[2],
                'priority'        => $t[3],
                'status'          => $t[4],
                'due_date'        => isset($t[5]) && $t[5] ? now()->modify($t[5])->toDateString() : null,
                'created_by'      => 2,
                'assigned_to'     => 2,
            ];
            if ($t[4] === 'completed' && isset($t[6])) {
                $data['completed_at'] = now()->modify($t[6]);
            }
            DpoTask::create($data);
        }

        // ── Compliance Checklist ─────────────────────────────────────────
        // [category, item, description, reference, status, sort]
        $checklistItems = [
            // ── Consent ─────────────────────────────────────────────────
            ['consent','จัดทำ Consent Form ที่ชัดเจนและแยกตาม Purpose','Consent ต้องขอแยกตามวัตถุประสงค์ ไม่รวมใน T&C','มาตรา 19','completed',1],
            ['consent','มีระบบบันทึก Consent ที่ตรวจสอบได้','Log วันที่ เวลา IP และวิธีการให้ความยินยอม','มาตรา 26','completed',2],
            ['consent','มีกระบวนการถอน Consent (Withdraw)','ง่ายพอๆ กับการให้ความยินยอม','มาตรา 19(3)','completed',3],
            ['consent','ระบุอายุขั้นต่ำสำหรับ Consent (20 ปี)','ผู้เยาว์ต้องได้รับความยินยอมจากผู้ปกครอง','มาตรา 20','in_progress',4],
            ['consent','Consent Template ผ่านการตรวจทางกฎหมาย','Legal review ทุก Template','มาตรา 19','completed',5],
            ['consent','มีกระบวนการ Re-consent เมื่อวัตถุประสงค์เปลี่ยน','แจ้งและขอ Consent ใหม่','มาตรา 19','not_started',6],

            // ── Rights ───────────────────────────────────────────────────
            ['rights','มี Channel รับ Data Subject Request','ช่องทาง Email/แบบฟอร์ม/Portal ที่ชัดเจน','มาตรา 30-36','completed',1],
            ['rights','มี SOP การตอบสนอง DSR ภายใน 30 วัน','ขั้นตอนการรับ ตรวจสอบ และตอบ Request','มาตรา 30','in_progress',2],
            ['rights','มีระบบ Track DSR ทุกคำขอ','บันทึกสถานะและ Deadline ทุก Request','มาตรา 30','completed',3],
            ['rights','รองรับสิทธิ์ขอลบข้อมูล (Right to Erasure)','กระบวนการลบข้อมูลและตรวจสอบ Legal Hold','มาตรา 33','not_started',4],
            ['rights','รองรับสิทธิ์โอนย้ายข้อมูล (Data Portability)','Export ข้อมูลในรูปแบบ Machine-readable','มาตรา 35','not_started',5],
            ['rights','มีกระบวนการ Verify ตัวตนก่อนตอบ Request','ป้องกันการเปิดเผยข้อมูลผิดคน','มาตรา 30','completed',6],

            // ── ROPA ─────────────────────────────────────────────────────
            ['ropa','จัดทำ ROPA ครบทุก Processing Activity','บันทึกตาม มาตรา 39 อย่างน้อย 1 ครั้งต่อปี','มาตรา 39','completed',1],
            ['ropa','ROPA ระบุฐานทางกฎหมาย (Legal Basis)','ระบุฐาน Consent/Contract/Legal Obligation ฯลฯ','มาตรา 24','completed',2],
            ['ropa','ROPA ระบุ Data Retention Period','กำหนดระยะเวลาเก็บข้อมูลชัดเจนทุก Process','มาตรา 37','in_progress',3],
            ['ropa','ROPA ระบุ Data Recipients ทั้งหมด','บันทึกผู้รับข้อมูลทั้งในและต่างประเทศ','มาตรา 39','in_progress',4],
            ['ropa','ทบทวน ROPA ทุก 12 เดือน','Update เมื่อมีการเปลี่ยนแปลง Process','มาตรา 39','not_started',5],
            ['ropa','ROPA ครอบคลุม Data Processor ทั้งหมด','รวม Vendor ที่รับประมวลผลข้อมูลแทน','มาตรา 40','not_started',6],

            // ── Breach ───────────────────────────────────────────────────
            ['breach','มีแผน Incident Response สำหรับ Data Breach','ขั้นตอนชัดเจนเมื่อเกิด Breach','มาตรา 37','in_progress',1],
            ['breach','มีกระบวนการแจ้ง PDPC ภายใน 72 ชั่วโมง','Template และ Process พร้อมใช้ทันที','มาตรา 37','in_progress',2],
            ['breach','มีกระบวนการแจ้งเจ้าของข้อมูลเมื่อเกิด Breach','แจ้งเมื่อมีความเสี่ยงสูงต่อสิทธิ์เสรีภาพ','มาตรา 37(3)','not_started',3],
            ['breach','จัดทำ Breach Register บันทึกทุกเหตุการณ์','บันทึกแม้ Breach เล็กน้อยที่ไม่ต้องแจ้ง','มาตรา 37','completed',4],
            ['breach','ทดสอบ Incident Response Plan ทุกปี','Tabletop Exercise หรือ Drill','มาตรา 37','not_started',5],

            // ── Security ─────────────────────────────────────────────────
            ['security','มีมาตรการ Encryption ข้อมูล At Rest','เข้ารหัสฐานข้อมูลที่มีข้อมูลส่วนบุคคล','มาตรา 37','completed',1],
            ['security','มีมาตรการ Encryption ข้อมูล In Transit','HTTPS/TLS ทุก Endpoint','มาตรา 37','completed',2],
            ['security','มี Access Control และ RBAC','กำหนดสิทธิ์ตาม Role Need-to-Know','มาตรา 37','completed',3],
            ['security','มี Audit Log การเข้าถึงข้อมูล','Log ทุก Query/Access พร้อม Retention','มาตรา 37','in_progress',4],
            ['security','มี Vulnerability Assessment ทุก 6 เดือน','Pen Test และ VA ระบบที่มีข้อมูลส่วนบุคคล','มาตรา 37','not_started',5],
            ['security','มี MFA สำหรับระบบที่เข้าถึงข้อมูล Sensitive','บังคับ MFA สำหรับ Admin และข้อมูลอ่อนไหว','มาตรา 37','in_progress',6],
            ['security','มีนโยบาย BYOD และ Mobile Security','ควบคุมอุปกรณ์ส่วนตัวที่เข้าถึงข้อมูล','มาตรา 37','not_started',7],

            // ── Policy ───────────────────────────────────────────────────
            ['policy','จัดทำ Privacy Policy เผยแพร่สาธารณะ','เว็บไซต์และ App ต้องมี Privacy Policy','มาตรา 23','completed',1],
            ['policy','Privacy Policy ระบุครบตาม ม.23','ชื่อผู้ควบคุม วัตถุประสงค์ ระยะเวลา สิทธิ์ ฯลฯ','มาตรา 23','completed',2],
            ['policy','มี Cookie Policy / Cookie Banner','แจ้งประเภท Cookie และขอ Consent','มาตรา 19','completed',3],
            ['policy','มี Employee Privacy Notice','แจ้งพนักงานเกี่ยวกับการใช้ข้อมูล','มาตรา 23','completed',4],
            ['policy','มี CCTV Notice ในพื้นที่ติดตั้ง','ป้ายแจ้งเตือนทุกจุดที่ติดกล้อง','มาตรา 23','in_progress',5],
            ['policy','ทบทวน Policy ทุก 12 เดือน','Update เมื่อกฎหมาย/ระบบเปลี่ยน','มาตรา 23','not_started',6],

            // ── Training ─────────────────────────────────────────────────
            ['training','จัดอบรม PDPA พนักงานใหม่ทุกคน','Onboarding Training ก่อนเริ่มงาน','มาตรา 40','completed',1],
            ['training','จัดอบรม Refresher ทุก 12 เดือน','อบรมซ้ำพนักงานเดิมทุกปี','มาตรา 40','in_progress',2],
            ['training','อบรมเชิงลึกสำหรับ IT/Dev Team','Security Awareness และ Privacy by Design','มาตรา 37','in_progress',3],
            ['training','มีการทดสอบความรู้หลังอบรม','Quiz/Assessment วัดความเข้าใจ','มาตรา 40','not_started',4],
            ['training','บันทึก Training Completion ทุกคน','ประวัติการอบรมครบทุกคน','มาตรา 40','in_progress',5],
            ['training','จัดทำ Training Material เป็นภาษาไทย','เอกสารและ VDO ภาษาไทย','มาตรา 40','completed',6],

            // ── Vendor ───────────────────────────────────────────────────
            ['vendor','จัดทำ Data Processing Agreement (DPA) กับ Vendor ทุกราย','DPA ต้องครอบคลุมทุก Processor','มาตรา 41','in_progress',1],
            ['vendor','ตรวจสอบมาตรการรักษาความปลอดภัยของ Vendor','Vendor Assessment ก่อน Engage','มาตรา 41','not_started',2],
            ['vendor','มี Vendor List และ Register ที่อัปเดต','บัญชีรายชื่อ Processor ทั้งหมด','มาตรา 40','in_progress',3],
            ['vendor','ทบทวน DPA ทุกปี หรือเมื่อ Vendor เปลี่ยน','ต่ออายุและปรับปรุง DPA','มาตรา 41','not_started',4],
            ['vendor','กำหนดข้อจำกัด Sub-processing','Vendor ต้องขออนุญาตก่อนส่งต่อข้อมูล','มาตรา 41','not_started',5],
            ['vendor','ตรวจสอบ Cross-border Transfer Safeguards','ประเมิน Adequacy Decision / SCCs','มาตรา 28','not_started',6],
        ];

        foreach ($checklistItems as [$cat, $item, $desc, $ref, $status, $sort]) {
            ComplianceChecklist::create([
                'organization_id' => 1,
                'category'        => $cat,
                'item'            => $item,
                'description'     => $desc,
                'reference'       => $ref,
                'status'          => $status,
                'sort_order'      => $sort,
                'completed_at'    => $status === 'completed' ? now()->subDays(rand(5,60))->toDateString() : null,
                'responsible_user'=> 2,
            ]);
        }

        $taskCount = DpoTask::where('organization_id', 1)->count();
        $clCount   = ComplianceChecklist::where('organization_id', 1)->count();
        $overdue   = DpoTask::where('organization_id', 1)->overdue()->count();
        $this->command->info("✅ DPO Seeded: {$taskCount} tasks ({$overdue} overdue) + {$clCount} checklist items — all org 1");
    }
}
