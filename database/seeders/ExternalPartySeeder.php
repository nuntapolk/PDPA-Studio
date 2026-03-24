<?php
namespace Database\Seeders;

use App\Models\DataProcessingAgreement;
use App\Models\ExternalParty;
use App\Models\ExternalPartyAssessment;
use App\Models\RopaRecord;
use App\Models\User;
use Illuminate\Database\Seeder;

class ExternalPartySeeder extends Seeder
{
    public function run(): void
    {
        \DB::table('ropa_external_parties')->truncate();
        \DB::table('consent_external_parties')->truncate();
        ExternalPartyAssessment::query()->delete();
        DataProcessingAgreement::withTrashed()->forceDelete();
        ExternalParty::withTrashed()->forceDelete();

        $adminId = User::first()->id;

        $parties = [
            // ── DATA PROCESSORS (เราเป็น DC จ้างเขาเป็น DP) ──────────────────
            [
                'code'=>'EP-0001','name'=>'Amazon Web Services (Thailand)','name_en'=>'Amazon Web Services',
                'type'=>'company','tax_id'=>'0105561136612','country'=>'TH','industry'=>'Cloud Computing',
                'relationship_type'=>'data_processor',
                'services_description'=>'Cloud hosting, S3 storage, RDS database, EC2 compute สำหรับระบบ PDPA Studio',
                'data_types_shared'=>['personal','contact','authentication'],
                'processing_purposes'=>['storage','backup','hosting'],
                'systems_involved'=>['S3','RDS','EC2','CloudFront'],
                'risk_level'=>'high','is_cross_border'=>true,
                'transfer_mechanism'=>'scc','transfer_countries'=>['SG','US'],
                'tia_required'=>true,'tia_completed_at'=>'2024-06-01',
                'website'=>'https://aws.amazon.com','contact_name'=>'AWS Support',
                'contact_email'=>'aws-privacy@amazon.com',
                'dpo_name'=>'AWS Data Protection','dpo_email'=>'aws-dpo@amazon.com',
                'status'=>'active','review_frequency_months'=>12,
                'next_review_date'=>now()->addMonths(8)->toDateString(),
                'relationship_started_at'=>'2022-01-01',
                'dpa'=>['title'=>'AWS DPA 2024','type'=>'dpa','our_role'=>'controller','their_role'=>'processor',
                        'status'=>'active','signed_at'=>'2024-01-15','effective_at'=>'2024-01-15',
                        'expires_at'=>'2026-01-14','dpa_number'=>'DPA-2024-001','version'=>'2.0',
                        'sub_processors_allowed'=>true,'breach_notification_hours'=>72],
            ],
            [
                'code'=>'EP-0002','name'=>'SendGrid (Twilio Thailand)','name_en'=>'Twilio SendGrid',
                'type'=>'company','country'=>'TH','industry'=>'Email Service Provider',
                'relationship_type'=>'data_processor',
                'services_description'=>'บริการส่งอีเมลอัตโนมัติ — Transactional email, Privacy Notice delivery, Certificate emails',
                'data_types_shared'=>['name','email','consent_reference'],
                'processing_purposes'=>['email_delivery','notification'],
                'risk_level'=>'medium','is_cross_border'=>true,
                'transfer_mechanism'=>'scc','transfer_countries'=>['US'],
                'website'=>'https://sendgrid.com','contact_email'=>'privacy@sendgrid.com',
                'status'=>'active','review_frequency_months'=>12,
                'next_review_date'=>now()->addMonths(3)->toDateString(),
                'relationship_started_at'=>'2023-03-01',
                'dpa'=>['title'=>'SendGrid DPA 2023','type'=>'dpa','our_role'=>'controller','their_role'=>'processor',
                        'status'=>'active','signed_at'=>'2023-03-15','effective_at'=>'2023-03-15',
                        'expires_at'=>now()->addDays(45)->toDateString(),'dpa_number'=>'DPA-2023-004','version'=>'1.0',
                        'sub_processors_allowed'=>false,'breach_notification_hours'=>72],
            ],
            [
                'code'=>'EP-0003','name'=>'บริษัท 2C2P (Thailand) จำกัด','name_en'=>'2C2P Thailand',
                'type'=>'company','tax_id'=>'0105546066563','country'=>'TH','industry'=>'Payment Gateway',
                'relationship_type'=>'data_processor',
                'services_description'=>'ประมวลผลการชำระเงิน, จัดเก็บข้อมูลบัตรเครดิต (PCI-DSS compliant)',
                'data_types_shared'=>['financial','payment_card','name','email'],
                'processing_purposes'=>['payment_processing'],
                'risk_level'=>'high','is_cross_border'=>false,
                'website'=>'https://www.2c2p.com',
                'contact_name'=>'ทีม Compliance','contact_email'=>'dpo@2c2p.com',
                'dpo_name'=>'วิภาวี ชัยมงคล','dpo_email'=>'dpo@2c2p.com','dpo_phone'=>'02-123-4567',
                'status'=>'active','review_frequency_months'=>6,
                'next_review_date'=>now()->addMonths(4)->toDateString(),
                'relationship_started_at'=>'2021-08-01',
                'dpa'=>['title'=>'DPA กับ 2C2P 2024','type'=>'dpa','our_role'=>'controller','their_role'=>'processor',
                        'status'=>'active','signed_at'=>'2024-02-01','effective_at'=>'2024-02-01',
                        'expires_at'=>'2026-01-31','dpa_number'=>'DPA-2024-002','version'=>'3.0',
                        'sub_processors_allowed'=>false,'audit_rights'=>true,'breach_notification_hours'=>24],
            ],
            [
                'code'=>'EP-0004','name'=>'Google LLC (Analytics & Workspace)','name_en'=>'Google LLC',
                'type'=>'company','country'=>'US','industry'=>'Technology',
                'relationship_type'=>'data_processor',
                'services_description'=>'Google Analytics 4 — วิเคราะห์การใช้งานเว็บ, Google Workspace — อีเมลและเอกสารองค์กร',
                'data_types_shared'=>['device_id','ip_address','behavior_data','email'],
                'processing_purposes'=>['analytics','productivity_tools'],
                'risk_level'=>'medium','is_cross_border'=>true,
                'transfer_mechanism'=>'scc','transfer_countries'=>['US','SG','IE'],
                'tia_required'=>true,'tia_completed_at'=>'2024-01-01',
                'website'=>'https://google.com','contact_email'=>'privacy@google.com',
                'dpo_name'=>'Google DPO','dpo_email'=>'googledpo@google.com',
                'status'=>'active','review_frequency_months'=>12,
                'next_review_date'=>now()->addMonths(9)->toDateString(),
                'relationship_started_at'=>'2020-01-01',
                'dpa'=>['title'=>'Google Workspace DPA','type'=>'dpa','our_role'=>'controller','their_role'=>'processor',
                        'status'=>'active','signed_at'=>'2024-01-01','effective_at'=>'2024-01-01',
                        'expires_at'=>'2025-12-31','dpa_number'=>'DPA-2024-003','version'=>'1.5',
                        'sub_processors_allowed'=>true,'breach_notification_hours'=>72],
            ],
            [
                'code'=>'EP-0005','name'=>'บริษัท ไอที เซอร์วิส จำกัด','name_en'=>'IT Service Co., Ltd.',
                'type'=>'company','tax_id'=>'0105556078901','country'=>'TH','industry'=>'IT Outsourcing',
                'relationship_type'=>'sub_processor',
                'services_description'=>'ให้บริการ IT Support, ดูแลรักษาระบบ, Server maintenance',
                'data_types_shared'=>['system_logs','user_accounts'],
                'processing_purposes'=>['it_support','system_maintenance'],
                'risk_level'=>'medium','is_cross_border'=>false,
                'contact_name'=>'สมชาย รักดี','contact_email'=>'somchai@itservice.co.th','contact_phone'=>'02-999-0000',
                'status'=>'active','review_frequency_months'=>12,
                'next_review_date'=>now()->addMonths(6)->toDateString(),
                'relationship_started_at'=>'2023-01-01',
                'dpa'=>['title'=>'Sub-Processor Agreement — IT Service','type'=>'dpa','our_role'=>'processor','their_role'=>'processor',
                        'status'=>'active','signed_at'=>'2023-01-10','effective_at'=>'2023-01-10',
                        'expires_at'=>'2025-12-31','dpa_number'=>'DPA-2023-005','version'=>'1.0',
                        'sub_processors_allowed'=>false,'audit_rights'=>true,'breach_notification_hours'=>48],
            ],

            // ── DATA CONTROLLERS (เขาเป็น DC จ้างเราเป็น DP) ──────────────────
            [
                'code'=>'EP-0006','name'=>'บริษัท อัลฟ่า รีเทล จำกัด (มหาชน)','name_en'=>'Alpha Retail PCL',
                'type'=>'company','tax_id'=>'0107550000001','country'=>'TH','industry'=>'Retail',
                'relationship_type'=>'data_controller',
                'services_description'=>'เราให้บริการ PDPA Compliance Platform แก่ Alpha Retail — ประมวลผลข้อมูลสมาชิกและลูกค้า',
                'data_types_shared'=>['personal','contact','purchase_history','loyalty_points'],
                'processing_purposes'=>['pdpa_compliance','consent_management','rights_management'],
                'risk_level'=>'high','is_cross_border'=>false,
                'contact_name'=>'คุณพิมพ์ใจ สุขสันต์','contact_email'=>'pimjai@alpha-retail.co.th','contact_phone'=>'02-111-2222',
                'dpo_name'=>'ดร.วิชา ตรงใจ','dpo_email'=>'dpo@alpha-retail.co.th','dpo_phone'=>'02-111-3333',
                'status'=>'active','review_frequency_months'=>6,
                'next_review_date'=>now()->addMonths(2)->toDateString(),
                'relationship_started_at'=>'2023-06-01',
                'dpa'=>['title'=>'DPA Alpha Retail ← เราเป็น DP','type'=>'dpa','our_role'=>'processor','their_role'=>'controller',
                        'status'=>'active','signed_at'=>'2023-06-01','effective_at'=>'2023-06-01',
                        'expires_at'=>'2025-05-31','dpa_number'=>'DPA-2023-001','version'=>'2.1',
                        'sub_processors_allowed'=>true,'audit_rights'=>true,'breach_notification_hours'=>72],
            ],
            [
                'code'=>'EP-0007','name'=>'โรงพยาบาลสุขภาพดี','name_en'=>'Sukkhapadi Hospital',
                'type'=>'company','tax_id'=>'0993000123456','country'=>'TH','industry'=>'Healthcare',
                'relationship_type'=>'data_controller',
                'services_description'=>'เราให้บริการระบบจัดการความยินยอมผู้ป่วย (PDPA Health Module)',
                'data_types_shared'=>['health','personal','contact','national_id'],
                'processing_purposes'=>['consent_management','patient_rights'],
                'risk_level'=>'critical','is_cross_border'=>false,
                'contact_name'=>'ผอ.สุพิชฌาย์ แพทย์ดี','contact_email'=>'director@sukkhapadi.co.th',
                'dpo_name'=>'พว.สมศรี ใจดี','dpo_email'=>'dpo@sukkhapadi.co.th',
                'status'=>'active','review_frequency_months'=>6,
                'next_review_date'=>now()->addDays(20)->toDateString(),
                'relationship_started_at'=>'2024-01-01',
                'dpa'=>['title'=>'DPA โรงพยาบาลสุขภาพดี','type'=>'dpa','our_role'=>'processor','their_role'=>'controller',
                        'status'=>'active','signed_at'=>'2024-01-05','effective_at'=>'2024-01-05',
                        'expires_at'=>'2025-12-31','dpa_number'=>'DPA-2024-006','version'=>'1.0',
                        'audit_rights'=>true,'breach_notification_hours'=>24],
            ],

            // ── JOINT CONTROLLERS ─────────────────────────────────────────────
            [
                'code'=>'EP-0008','name'=>'บริษัท เบต้า โซลูชัน จำกัด (บริษัทในเครือ)','name_en'=>'Beta Solution Ltd.',
                'type'=>'company','tax_id'=>'0105565012345','country'=>'TH','industry'=>'Technology',
                'relationship_type'=>'joint_controller',
                'services_description'=>'บริษัทในเครือ — ร่วมควบคุมข้อมูลลูกค้ากลุ่มธุรกิจ, ใช้ฐานข้อมูลลูกค้าร่วมกัน',
                'data_types_shared'=>['personal','contact','purchase_history','subscription'],
                'processing_purposes'=>['shared_customer_database','cross_selling'],
                'risk_level'=>'medium','is_cross_border'=>false,
                'contact_name'=>'กรรมการผู้จัดการ','contact_email'=>'ceo@beta-solution.co.th',
                'dpo_name'=>'วรรณา กฎหมาย','dpo_email'=>'legal@beta-solution.co.th',
                'status'=>'active','review_frequency_months'=>12,
                'next_review_date'=>now()->addMonths(10)->toDateString(),
                'relationship_started_at'=>'2022-07-01',
                'dpa'=>['title'=>'Joint Controller Agreement — Beta Solution','type'=>'jca','our_role'=>'joint_controller','their_role'=>'joint_controller',
                        'status'=>'active','signed_at'=>'2022-07-01','effective_at'=>'2022-07-01',
                        'expires_at'=>'2024-06-30','dpa_number'=>'JCA-2022-001','version'=>'1.0'],
            ],

            // ── RECIPIENTS ─────────────────────────────────────────────────────
            [
                'code'=>'EP-0009','name'=>'กรมสรรพากร','name_en'=>'Revenue Department',
                'type'=>'government','country'=>'TH','industry'=>'Government',
                'relationship_type'=>'recipient',
                'services_description'=>'เปิดเผยข้อมูลรายได้และเงินเดือนพนักงาน ตามภาระผูกพันทางกฎหมาย',
                'data_types_shared'=>['financial','salary','national_id','name'],
                'processing_purposes'=>['tax_reporting','legal_obligation'],
                'risk_level'=>'low','is_cross_border'=>false,
                'website'=>'https://www.rd.go.th',
                'contact_name'=>'ศูนย์บริการ','contact_email'=>'info@rd.go.th',
                'status'=>'active','review_frequency_months'=>24,
                'next_review_date'=>now()->addMonths(18)->toDateString(),
                'relationship_started_at'=>'2019-01-01',
                'dpa'=>null, // ไม่ต้องทำ DPA กับหน่วยงานรัฐ
            ],
            [
                'code'=>'EP-0010','name'=>'สำนักงานคณะกรรมการคุ้มครองข้อมูลส่วนบุคคล (PDPC)','name_en'=>'PDPC Thailand',
                'type'=>'supervisory_authority','country'=>'TH','industry'=>'Government',
                'relationship_type'=>'supervisory_authority',
                'services_description'=>'หน่วยงานกำกับดูแล PDPA — รายงานเหตุการณ์ละเมิดข้อมูล (Data Breach) ภายใน 72 ชั่วโมง',
                'data_types_shared'=>['breach_reports','compliance_reports'],
                'processing_purposes'=>['regulatory_reporting','breach_notification'],
                'risk_level'=>'low','is_cross_border'=>false,
                'website'=>'https://www.pdpc.or.th',
                'contact_email'=>'pdpc@pdpc.or.th','contact_phone'=>'02-142-1033',
                'status'=>'active','review_frequency_months'=>0,
                'next_review_date'=>null,
                'relationship_started_at'=>'2022-06-01',
                'dpa'=>null,
            ],
            [
                'code'=>'EP-0011','name'=>'บริษัท ประกันภัย เอบีซี จำกัด','name_en'=>'ABC Insurance Co., Ltd.',
                'type'=>'company','tax_id'=>'0107540000099','country'=>'TH','industry'=>'Insurance',
                'relationship_type'=>'recipient',
                'services_description'=>'รับข้อมูลพนักงานเพื่อทำประกันกลุ่ม — ชื่อ อายุ สุขภาพพื้นฐาน',
                'data_types_shared'=>['personal','health','employment'],
                'processing_purposes'=>['group_insurance'],
                'risk_level'=>'medium','is_cross_border'=>false,
                'contact_name'=>'ทีม HR Benefits','contact_email'=>'group@abc-insurance.co.th',
                'dpo_email'=>'dpo@abc-insurance.co.th',
                'status'=>'active','review_frequency_months'=>12,
                'next_review_date'=>now()->addMonths(7)->toDateString(),
                'relationship_started_at'=>'2021-01-01',
                'dpa'=>['title'=>'Data Sharing Agreement — ABC Insurance','type'=>'data_sharing_agreement',
                        'our_role'=>'controller','their_role'=>'controller',
                        'status'=>'active','signed_at'=>'2021-01-01','effective_at'=>'2021-01-01',
                        'expires_at'=>'2025-12-31','dpa_number'=>'DSA-2021-001','version'=>'1.0'],
            ],
            // ── PENDING (No DPA yet) ───────────────────────────────────────────
            [
                'code'=>'EP-0012','name'=>'บริษัท CRM Pro จำกัด','name_en'=>'CRM Pro Co., Ltd.',
                'type'=>'company','country'=>'TH','industry'=>'CRM Software',
                'relationship_type'=>'data_processor',
                'services_description'=>'ระบบ CRM สำหรับจัดการลูกค้า — อยู่ระหว่างการเจรจา DPA',
                'data_types_shared'=>['personal','contact','purchase_history'],
                'processing_purposes'=>['crm','customer_management'],
                'risk_level'=>'high','is_cross_border'=>false,
                'status'=>'under_review','review_frequency_months'=>6,
                'next_review_date'=>now()->addDays(14)->toDateString(),
                'relationship_started_at'=>now()->toDateString(),
                'notes'=>'อยู่ระหว่างรอ DPA ที่ลงนาม — ห้ามส่งข้อมูลจนกว่าจะ signed',
                'dpa'=>['title'=>'CRM Pro DPA (Draft)','type'=>'dpa','our_role'=>'controller','their_role'=>'processor',
                        'status'=>'draft','dpa_number'=>'DPA-2024-DRAFT-001','version'=>'0.1'],
            ],
        ];

        foreach ($parties as $data) {
            $dpaData = $data['dpa'] ?? null;
            unset($data['dpa']);
            $data['created_by'] = $adminId;
            if (!isset($data['relationship_type'])) continue;

            // Override type for supervisory authority
            if ($data['relationship_type'] === 'supervisory_authority') {
                $data['type'] = 'government';
            }

            $party = ExternalParty::create($data);

            // Create DPA
            if ($dpaData) {
                DataProcessingAgreement::create(array_merge($dpaData, [
                    'external_party_id' => $party->id,
                    'created_by'        => $adminId,
                    'data_categories'   => $data['data_types_shared'] ?? [],
                    'processing_purposes'=> $data['processing_purposes'] ?? [],
                ]));
            }

            // Create assessment for active parties
            if ($party->status === 'active' && in_array($party->relationship_type, ['data_processor','data_controller','joint_controller'])) {
                ExternalPartyAssessment::create([
                    'external_party_id'   => $party->id,
                    'assessed_by'         => $adminId,
                    'assessment_type'     => 'initial',
                    'score'               => match($party->risk_level) { 'low'=>90, 'medium'=>72, 'high'=>58, 'critical'=>40, default=>70 },
                    'risk_level'          => $party->risk_level,
                    'findings'            => "ผลการประเมินเบื้องต้น — {$party->name} มีระดับความเสี่ยง {$party->risk_level}",
                    'recommendations'     => $party->risk_level === 'high' || $party->risk_level === 'critical'
                        ? 'ควรเพิ่มมาตรการรักษาความปลอดภัย และตรวจสอบ Sub-Processors' : 'ดำเนินการปกติ ทบทวนประจำปี',
                    'next_assessment_date'=> now()->addMonths($party->review_frequency_months ?? 12)->toDateString(),
                ]);
            }
        }

        // Link some external parties to ROPA records
        $ropaRecords = RopaRecord::where('organization_id', 1)->take(10)->get();
        $processors  = ExternalParty::where('relationship_type','data_processor')->get();
        foreach ($ropaRecords as $i => $ropa) {
            $party = $processors->get($i % $processors->count());
            if ($party) {
                \DB::table('ropa_external_parties')->insertOrIgnore([
                    'ropa_record_id'   => $ropa->id,
                    'external_party_id'=> $party->id,
                    'party_role'       => 'processor',
                    'data_categories'  => json_encode($party->data_types_shared ?? []),
                    'purpose'          => $ropa->purpose ?? 'ประมวลผลตามวัตถุประสงค์ใน ROPA',
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]);
            }
        }

        // Update org1 pdpa role
        \App\Models\Organization::find(1)?->update([
            'primary_pdpa_role' => 'both',
            'legal_rep_name'    => 'นายสมชาย กรรมการผู้จัดการ',
            'privacy_email'     => 'privacy@company.co.th',
            'privacy_phone'     => '02-888-9999',
            'dpo_appointed_at'  => '2022-06-01',
            'pdpa_registration_no' => 'PDPC-DC-2022-00123',
        ]);

        $this->command->info(sprintf(
            'ExternalPartySeeder: %d parties, %d DPAs, %d assessments, %d ROPA links',
            ExternalParty::count(), DataProcessingAgreement::count(),
            ExternalPartyAssessment::count(),
            \DB::table('ropa_external_parties')->count()
        ));
    }
}
