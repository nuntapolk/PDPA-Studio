<?php

namespace Database\Seeders;

use App\Models\Assessment;
use App\Models\AssessmentSection;
use App\Models\AssessmentQuestion;
use Illuminate\Database\Seeder;

class AssessmentSeeder extends Seeder
{
    public function run(): void
    {
        Assessment::query()->forceDelete();
        AssessmentSection::query()->delete();
        AssessmentQuestion::query()->delete();

        // ── DPIA template sections (used for all 50 DPIAs) ─────────────────
        $dpiaTemplate = [
            ['title' => '1. ข้อมูลระบบและการประมวลผล', 'questions' => [
                ['question' => 'ระบบ/กระบวนการนี้ประมวลผลข้อมูลส่วนบุคคลประเภทใด?',             'answer_type' => 'text',   'risk_score' => 0],
                ['question' => 'มีการประมวลผลข้อมูลอ่อนไหว (มาตรา 26) หรือไม่?',                'answer_type' => 'yes_no', 'risk_score' => 15],
                ['question' => 'มีการทำ Profiling หรือ Automated Decision Making หรือไม่?',      'answer_type' => 'yes_no', 'risk_score' => 12],
                ['question' => 'มีการประมวลผลข้อมูลของเด็กอายุต่ำกว่า 20 ปี หรือไม่?',          'answer_type' => 'yes_no', 'risk_score' => 10],
                ['question' => 'มีการส่งข้อมูลไปต่างประเทศหรือไม่?',                            'answer_type' => 'yes_no', 'risk_score' => 8],
                ['question' => 'จำนวนเจ้าของข้อมูลที่ได้รับผลกระทบ (ประมาณ)',                   'answer_type' => 'text',   'risk_score' => 0],
            ]],
            ['title' => '2. ฐานทางกฎหมายและความจำเป็น', 'questions' => [
                ['question' => 'ฐานทางกฎหมายที่ใช้สำหรับการประมวลผลคืออะไร?',                  'answer_type' => 'text',   'risk_score' => 0],
                ['question' => 'การประมวลผลมีความจำเป็นและได้สัดส่วนกับวัตถุประสงค์หรือไม่?',  'answer_type' => 'yes_no', 'risk_score' => 8],
                ['question' => 'มีการเก็บข้อมูลมากกว่าที่จำเป็น (Over-collection) หรือไม่?',   'answer_type' => 'yes_no', 'risk_score' => 10],
                ['question' => 'ระยะเวลาเก็บรักษาข้อมูลมีความเหมาะสมหรือไม่?',                 'answer_type' => 'yes_no', 'risk_score' => 6],
            ]],
            ['title' => '3. การระบุและประเมินความเสี่ยง', 'questions' => [
                ['question' => 'มีความเสี่ยงด้าน Unauthorized Access หรือ Data Breach หรือไม่?', 'answer_type' => 'yes_no', 'risk_score' => 12],
                ['question' => 'มีความเสี่ยงที่เจ้าของข้อมูลจะถูกเลือกปฏิบัติหรือไม่?',        'answer_type' => 'yes_no', 'risk_score' => 10],
                ['question' => 'มีความเสี่ยงด้านความถูกต้องของข้อมูล (Data Quality) หรือไม่?',  'answer_type' => 'yes_no', 'risk_score' => 6],
                ['question' => 'ระดับความเสี่ยงโดยรวมที่ประเมินได้ (0=ต่ำ, 10=สูงมาก)',         'answer_type' => 'scale',  'risk_score' => 15],
            ]],
            ['title' => '4. มาตรการลดความเสี่ยง', 'questions' => [
                ['question' => 'มีมาตรการ Encryption สำหรับข้อมูลในระบบหรือไม่?',               'answer_type' => 'yes_no', 'risk_score' => 0],
                ['question' => 'มีการกำหนด Access Control และ RBAC ที่เหมาะสมหรือไม่?',         'answer_type' => 'yes_no', 'risk_score' => 0],
                ['question' => 'มีระบบ Audit Log สำหรับการเข้าถึงข้อมูลหรือไม่?',               'answer_type' => 'yes_no', 'risk_score' => 0],
                ['question' => 'มีแผน Data Breach Response ที่ชัดเจนหรือไม่?',                  'answer_type' => 'yes_no', 'risk_score' => 0],
                ['question' => 'มาตรการเพิ่มเติมที่วางแผนจะดำเนินการ',                          'answer_type' => 'text',   'risk_score' => 0],
            ]],
        ];

        // ── Helper: build answer set for a given scenario ─────────────────
        // [dataType, hasSensitive, hasProfiling, hasChildren, hasCrossBorder, dataCount,
        //  legalBasis, isNecessary, overCollection, retentionOk,
        //  breachRisk, discrimination, dataQuality, riskScale,
        //  encryption, rbac, auditLog, breachPlan, mitigation]
        $ans = fn(array $v) => $v;

        // ── 50 DPIA records ────────────────────────────────────────────────
        // Columns: [num, title, scope, status, risk_level, risk_score, findings, recommendations, months_ago_start, months_ago_complete, answers]
        // answers = [dataType, sensitive, profiling, children, crossBorder, count, legalBasis, necessary, overCollect, retention, breach, discrim, dataQ, scale, encrypt, rbac, audit, breachPlan, mitigation]
        $dpias = [
            // ── Retail / E-commerce (1-10) ──────────────────────────────────
            ['DPIA-2026-001','DPIA: ระบบ AI Recommendation Engine','ระบบ Recommendation วิเคราะห์พฤติกรรมลูกค้า','approved','medium',55,
                'พบความเสี่ยงปานกลางจากการสร้าง Profile ลูกค้า','เพิ่มมาตรการ Data Minimization และ Anonymization',2,1,
                ['ประวัติการซื้อ พฤติกรรมการดูสินค้า Cookie Device ID','no','yes','no','no','500,000 ราย','Consent + Legitimate Interest','yes','no','yes','yes','no','no','5','yes','yes','yes','yes','ใช้ Differential Privacy']],
            ['DPIA-2026-002','DPIA: ระบบ Loyalty Program และสะสมแต้ม','ระบบ CRM และ Loyalty สำหรับลูกค้า','approved','low',22,
                'ความเสี่ยงต่ำ ข้อมูลที่เก็บจำเป็นและมีฐานกฎหมายชัดเจน','รักษามาตรการปัจจุบัน ทบทวนทุก 12 เดือน',4,3,
                ['ชื่อ อีเมล เบอร์โทร ประวัติการซื้อ แต้มสะสม','no','no','no','no','200,000 ราย','Consent','yes','no','yes','no','no','no','2','yes','yes','yes','yes','ไม่มีเพิ่มเติม']],
            ['DPIA-2026-003','DPIA: ระบบ Payment Gateway และการชำระเงิน','ระบบรับชำระเงิน Online ทุกช่องทาง','approved','high',72,
                'พบความเสี่ยงสูงจากข้อมูลการเงินและบัตรเครดิต','ขอรับ PCI DSS Certification และเข้ารหัสข้อมูลบัตรทั้งหมด',6,5,
                ['ข้อมูลบัตรเครดิต เลขบัญชี ข้อมูลการเงิน','no','no','no','yes','1,000,000 ราย','Contract + Legal Obligation','yes','no','yes','yes','no','yes','7','yes','yes','yes','yes','PCI DSS, Tokenization']],
            ['DPIA-2026-004','DPIA: ระบบ CCTV ในร้านค้า','กล้อง CCTV 50 ตัวในสาขาทั่วประเทศ','completed','medium',48,
                'ต้องปรับปรุงป้ายแจ้งเตือน และกำหนดระยะเก็บวิดีโอให้ชัดเจน','ติดป้ายแจ้งเตือนตาม PDPA และจำกัดการเก็บข้อมูลไม่เกิน 30 วัน',3,2,
                ['ภาพวิดีโอบุคคลในร้านค้า','no','no','no','no','ลูกค้าและพนักงานทุกวัน','Legitimate Interest','yes','no','yes','yes','no','no','4','yes','yes','yes','yes','ลบวิดีโออัตโนมัติหลัง 30 วัน']],
            ['DPIA-2026-005','DPIA: แอปมือถือ E-Commerce','App ที่เก็บข้อมูลผู้ใช้และ Location','completed','medium',52,
                'พบประเด็นการเก็บ Location Data ที่ไม่จำเป็นตลอดเวลา','ปรับเป็น Location เฉพาะเมื่อใช้งาน ไม่เก็บ Background Location',2,1,
                ['ชื่อ อีเมล Location ประวัติการซื้อ Device Info','no','no','no','no','750,000 ราย','Consent','yes','yes','yes','yes','no','no','5','yes','yes','no','yes','ใช้ Location Permission แบบ While Using Only']],
            ['DPIA-2026-006','DPIA: ระบบ Chatbot Customer Service','Chatbot AI ที่รับข้อมูลและตอบคำถามลูกค้า','in_progress','medium',null,null,null,1,null,
                ['ข้อมูลคำถาม ข้อร้องเรียน ข้อมูลการสั่งซื้อ','no','yes','no','yes','300,000 ราย',null,null,null,null,null,null,null,null,null,null,null,null,null]],
            ['DPIA-2026-007','DPIA: ระบบวิเคราะห์ราคาแบบ Dynamic Pricing','ระบบปรับราคาอัตโนมัติตามพฤติกรรมผู้ใช้','completed','high',68,
                'Dynamic Pricing อาจนำไปสู่การเลือกปฏิบัติ ต้องมีความโปร่งใส','เพิ่มการแจ้งให้ลูกค้าทราบเมื่อราคามีการปรับจาก Profiling',3,2,
                ['ประวัติการซื้อ พฤติกรรมการค้นหา ข้อมูล Device','no','yes','no','no','500,000 ราย','Legitimate Interest','yes','no','yes','yes','yes','yes','6','yes','yes','yes','yes','Pricing Transparency Dashboard']],
            ['DPIA-2026-008','DPIA: ระบบ Return/คืนสินค้า','กระบวนการรับคืนสินค้าและคืนเงิน','approved','low',15,
                'ความเสี่ยงต่ำ ข้อมูลเก็บเท่าที่จำเป็นสำหรับกระบวนการ','ไม่มีมาตรการเพิ่มเติมที่จำเป็น',5,4,
                ['ชื่อ ที่อยู่ เลขคำสั่งซื้อ เหตุผลการคืน','no','no','no','no','50,000 ราย','Contract','yes','no','yes','no','no','no','1','yes','yes','yes','yes','ไม่มี']],
            ['DPIA-2026-009','DPIA: ระบบ Affiliate Marketing','ระบบติดตาม Affiliate และ Partner','in_progress','medium',null,null,null,1,null,
                ['Cookie Tracking, Affiliate ID, ข้อมูลการคลิก','no','no','no','yes',null,null,null,null,null,null,null,null,null,null,null,null,null,null]],
            ['DPIA-2026-010','DPIA: ระบบ Email Marketing Automation','ระบบส่ง Email Campaign อัตโนมัติ','completed','low',28,
                'ความเสี่ยงต่ำ มีระบบ Unsubscribe ที่ชัดเจน','เพิ่ม Double Opt-in สำหรับ New Subscriber',6,5,
                ['อีเมล ชื่อ ประวัติการเปิด Email','no','no','no','no','400,000 ราย','Consent','yes','no','yes','no','no','no','2','yes','yes','yes','no','Double Opt-in Flow']],

            // ── โรงพยาบาล / สุขภาพ (11-20) ─────────────────────────────────
            ['DPIA-2026-011','DPIA: ระบบ HIS — Hospital Information System','ระบบ HIS ทั้งหมด EMR, Lab, Pharmacy','in_progress','very_high',null,null,null,0,null,
                ['ข้อมูลสุขภาพ ผลตรวจ ประวัติการรักษา','yes',null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null,null]],
            ['DPIA-2026-012','DPIA: ระบบ Telemedicine / การรักษาออนไลน์','ระบบนัดหมายและพบแพทย์ผ่าน Video Call','approved','high',75,
                'ข้อมูลสุขภาพที่ส่งผ่าน Internet มีความเสี่ยงสูง ต้องใช้ End-to-End Encryption','ใช้ E2E Encryption สำหรับ Video Call และ Chat ทั้งหมด',4,3,
                ['ข้อมูลสุขภาพ วิดีโอ การสนทนา ประวัติการรักษา','yes','no','yes','no','100,000 ราย','Consent + Legal Obligation','yes','no','yes','yes','no','no','7','yes','yes','yes','yes','E2E Encryption, Zero-Knowledge Architecture']],
            ['DPIA-2026-013','DPIA: ระบบ Wearable / Health Tracking','ข้อมูลจาก Smart Watch และ IoT ด้านสุขภาพ','completed','high',70,
                'ข้อมูล Biometric แบบ Continuous เป็น High-Risk Processing','ต้องแจ้งวัตถุประสงค์ชัดเจน และให้ผู้ใช้ควบคุมการแชร์ข้อมูล',3,2,
                ['Heart Rate, Sleep, Activity, Location, Biometric','yes','yes','no','yes','80,000 ราย','Consent','yes','no','yes','yes','no','no','7','yes','yes','yes','yes','On-device Processing, Data Minimization']],
            ['DPIA-2026-014','DPIA: ระบบ Lab และผลตรวจเลือด','ระบบ Laboratory Information Management','approved','medium',45,
                'ข้อมูลผลตรวจมีความอ่อนไหว แต่มีฐานกฎหมายชัดเจน','เพิ่มการ Audit การเข้าถึงผลตรวจอย่างสม่ำเสมอ',8,7,
                ['ผลตรวจเลือด ข้อมูลสุขภาพ เลขผู้ป่วย','yes','no','no','no','200,000 ราย','Consent + Legal Obligation','yes','no','yes','no','no','no','4','yes','yes','yes','yes','Audit Log ทุก 30 วัน']],
            ['DPIA-2026-015','DPIA: ระบบแจ้งเตือนผู้ป่วยและนัดหมาย','ระบบ SMS/Email แจ้งเตือนนัดหมาย','approved','low',18,
                'ความเสี่ยงต่ำ ข้อมูลที่ใช้น้อยและจำเป็น','ไม่มีมาตรการเพิ่มเติม',10,9,
                ['ชื่อ เบอร์โทร วันนัด','no','no','yes','no','150,000 ราย','Consent + Contract','yes','no','yes','no','no','no','1','yes','yes','no','yes','ไม่มี']],
            ['DPIA-2026-016','DPIA: ระบบแชร์ข้อมูลผู้ป่วยกับโรงพยาบาลเครือข่าย','การส่งต่อข้อมูลผู้ป่วยระหว่างสถานพยาบาล','in_progress','very_high',null,null,null,1,null,
                ['ข้อมูลสุขภาพครบถ้วน ประวัติการรักษา Diagnosis','yes',null,null,'yes',null,null,null,null,null,null,null,null,null,null,null,null,null,null]],
            ['DPIA-2026-017','DPIA: ระบบ AI วินิจฉัยโรค','AI ช่วยวินิจฉัยจากภาพ X-Ray และ MRI','completed','very_high',88,
                'AI Decision Making ด้านการแพทย์มีความเสี่ยงสูงมาก ต้องมีแพทย์ตรวจสอบเสมอ','บังคับมี Human Oversight ทุก Case ไม่ให้ AI ตัดสินใจขั้นสุดท้ายโดยลำพัง',5,4,
                ['ภาพ X-Ray MRI CT Scan ข้อมูลสุขภาพ','yes','yes','yes','no','50,000 ราย','Consent + Legal Obligation','yes','no','yes','yes','no','yes','9','yes','yes','yes','yes','Human-in-the-Loop Mandatory']],
            ['DPIA-2026-018','DPIA: ระบบ Pharmacy และใบสั่งยา','ระบบจัดการใบสั่งยาและประวัติการรับยา','approved','medium',42,
                'ข้อมูลยาที่ใช้อาจเปิดเผยโรคประจำตัว ต้องควบคุมการเข้าถึง','จำกัดสิทธิ์เข้าถึงข้อมูลยาเฉพาะเภสัชกรและแพทย์ที่รับผิดชอบ',7,6,
                ['ใบสั่งยา ประวัติยา ข้อมูลผู้ป่วย','yes','no','no','no','100,000 ราย','Consent + Legal Obligation','yes','no','yes','no','no','no','4','yes','yes','yes','yes','Role-Based Access ตามวิชาชีพ']],
            ['DPIA-2026-019','DPIA: ระบบวิจัยทางการแพทย์','การใช้ข้อมูลผู้ป่วยเพื่องานวิจัย','in_progress','high',null,null,null,2,null,
                ['ข้อมูลสุขภาพ ผลตรวจ DNA (บางกรณี)','yes','no','no','no',null,'Consent','yes',null,null,null,null,null,null,null,null,null,null,null]],
            ['DPIA-2026-020','DPIA: ระบบ Health Insurance Claim','กระบวนการเคลมประกันสุขภาพ','approved','high',65,
                'ข้อมูลสุขภาพที่แชร์กับบริษัทประกันมีความเสี่ยง ต้องมี DPA ที่รัดกุม','ทำ DPA กับบริษัทประกันทุกรายและ Audit ทุก 6 เดือน',9,8,
                ['ข้อมูลสุขภาพ ใบเสร็จ การวินิจฉัย','yes','no','no','no','200,000 ราย','Consent + Legal Obligation','yes','no','yes','yes','no','no','6','yes','yes','yes','yes','DPA ครบทุกคู่สัญญา']],

            // ── ประกันภัย / การเงิน (21-30) ─────────────────────────────────
            ['DPIA-2026-021','DPIA: ระบบประเมินความเสี่ยงประกันชีวิต','Underwriting System วิเคราะห์ความเสี่ยงลูกค้า','approved','high',70,
                'Automated Underwriting อาจนำไปสู่การเลือกปฏิบัติต้องตรวจสอบ','เพิ่ม Explainability สำหรับผลการตัดสินใจทุกรายการ',6,5,
                ['ข้อมูลสุขภาพ รายได้ อาชีพ ประวัติการเคลม','yes','yes','no','no','500,000 ราย','Contract + Consent','yes','no','yes','yes','yes','yes','7','yes','yes','yes','yes','XAI Dashboard สำหรับ Underwriter']],
            ['DPIA-2026-022','DPIA: ระบบ Fraud Detection ประกัน','AI ตรวจจับการเคลมประกันทุจริต','approved','medium',55,
                'การ Flag ผู้เอาประกันเป็น Fraudulent ต้องมีกระบวนการอุทธรณ์','เพิ่มกระบวนการ Manual Review ก่อนยกเลิกกรมธรรม์',4,3,
                ['ประวัติการเคลม พฤติกรรม ข้อมูลส่วนตัว','no','yes','no','no','800,000 ราย','Legitimate Interest','yes','no','yes','yes','no','yes','5','yes','yes','yes','yes','Appeal Process ใน 30 วัน']],
            ['DPIA-2026-023','DPIA: ระบบ KYC — Know Your Customer','กระบวนการยืนยันตัวตนลูกค้าใหม่','approved','medium',48,
                'ข้อมูล ID Card และ Selfie เป็นข้อมูลชีวมิติ ต้องดูแลเป็นพิเศษ','ลบ Selfie ทันทีหลัง Verify เสร็จ ไม่เก็บไว้ถาวร',3,2,
                ['บัตรประชาชน Selfie ข้อมูลที่อยู่','yes','no','no','no','200,000 ราย','Legal Obligation','yes','no','yes','yes','no','no','4','yes','yes','yes','yes','Auto-delete Biometric หลัง Verify']],
            ['DPIA-2026-024','DPIA: ระบบ AML — Anti-Money Laundering','ระบบตรวจจับ Transaction ผิดปกติ','completed','high',65,
                'การ Monitor Transaction อย่างต่อเนื่องต้องมีฐาน Legal Obligation ที่ชัดเจน','จัดทำ Record ของการ Monitor ทุกรายการตาม กฎหมาย AMLO',5,4,
                ['ข้อมูล Transaction ประวัติการโอน ข้อมูลบัญชี','no','yes','no','yes','1,000,000 ราย','Legal Obligation','yes','no','yes','yes','no','no','6','yes','yes','yes','yes','AMLO Compliance Documentation']],
            ['DPIA-2026-025','DPIA: ระบบ Credit Scoring','ระบบประเมิน Credit Score ลูกค้า','in_progress','high',null,null,null,1,null,
                ['ประวัติการชำระหนี้ ข้อมูลการเงิน ข้อมูลบัญชี','no','yes','no','no',null,'Legal Obligation + Consent',null,null,null,null,null,null,null,null,null,null,null,null]],
            ['DPIA-2026-026','DPIA: ระบบ Digital Wallet','กระเป๋าเงินดิจิทัลและการโอนเงิน','approved','medium',42,
                'ความเสี่ยงปานกลาง มีการเชื่อมต่อกับบัญชีธนาคาร','เพิ่ม Multi-Factor Authentication บังคับ',7,6,
                ['ข้อมูลบัญชี ประวัติ Transaction PIN/Biometric','yes','no','no','no','500,000 ราย','Contract + Consent','yes','no','yes','yes','no','no','4','yes','yes','yes','yes','MFA บังคับทุก Transaction']],
            ['DPIA-2026-027','DPIA: ระบบ Telesales ประกัน','การโทรขายประกันโดย Call Center','completed','low',25,
                'ต้องบันทึกความยินยอมก่อนนำเสนอผ่านโทรศัพท์','ใช้ระบบ Script ที่ขอ Consent ก่อนเสมอ',6,5,
                ['ชื่อ เบอร์โทร ข้อมูลการโทร','no','no','no','no','100,000 ราย','Consent','yes','no','yes','no','no','no','2','yes','yes','yes','no','Consent Recording ก่อนนำเสนอ']],
            ['DPIA-2026-028','DPIA: ระบบ Claims Processing อัตโนมัติ','AI ประมวลผลการเคลมอัตโนมัติ','completed','high',72,
                'Automated Decision ในการเคลมมีผลกระทบสูงต้องมี Human Review','บังคับ Human Review สำหรับการปฏิเสธเคลมทุกกรณี',4,3,
                ['ข้อมูลการเคลม ข้อมูลสุขภาพ หลักฐาน','yes','yes','no','no','300,000 ราย','Contract','yes','no','yes','yes','no','yes','7','yes','yes','yes','yes','Human-in-the-Loop สำหรับ Rejection']],
            ['DPIA-2026-029','DPIA: ระบบ Actuarial Data Analysis','การวิเคราะห์ข้อมูลคณิตศาสตร์ประกันภัย','approved','low',20,
                'ข้อมูลผ่านการ Anonymize แล้ว ความเสี่ยงต่ำ','ทบทวน Anonymization Method ทุกปี',12,11,
                ['ข้อมูล Aggregated/Anonymized','no','no','no','no','Statistical Data','Legal Obligation','yes','no','yes','no','no','no','2','yes','yes','yes','yes','ไม่มีเพิ่มเติม']],
            ['DPIA-2026-030','DPIA: ระบบ Bancassurance Partners','การแชร์ข้อมูลลูกค้ากับธนาคารพันธมิตร','in_progress','very_high',null,null,null,0,null,
                ['ข้อมูลส่วนตัว ข้อมูลการเงิน ข้อมูลประกัน','no','no','no','no',null,'Consent',null,null,null,null,null,null,null,null,null,null,null,null]],

            // ── รับเหมาก่อสร้าง / อสังหาฯ (31-38) ──────────────────────────
            ['DPIA-2026-031','DPIA: ระบบบริหารโครงการก่อสร้าง','ระบบจัดการโครงการและแรงงานก่อสร้าง','approved','low',18,
                'ความเสี่ยงต่ำ ข้อมูลที่ใช้จำเป็นสำหรับการบริหารโครงการ','ไม่มีมาตรการเพิ่มเติม',8,7,
                ['ชื่อ ที่อยู่ ข้อมูลการจ้างงาน Skill Set','no','no','no','no','5,000 คน','Contract','yes','no','yes','no','no','no','1','yes','yes','no','yes','ไม่มี']],
            ['DPIA-2026-032','DPIA: ระบบบันทึกเวลาทำงานพนักงาน','Time Attendance ด้วย Face Recognition','completed','high',68,
                'Face Recognition เป็น Biometric Data ที่อ่อนไหว ต้องมีความยินยอม','ขอ Consent จากพนักงานทุกคนก่อน และเสนอทางเลือกอื่น (บัตร/PIN)',3,2,
                ['ข้อมูลชีวมิติ (ใบหน้า) เวลาเข้า-ออก','yes','no','no','no','2,000 คน','Consent','yes','no','yes','yes','no','no','6','yes','yes','yes','yes','ทางเลือก: บัตร RFID หรือ PIN']],
            ['DPIA-2026-033','DPIA: ระบบ GPS ติดตามยานพาหนะ','GPS Tracking รถก่อสร้างและพนักงานขับรถ','approved','medium',45,
                'GPS Tracking พนักงานต้องแจ้งให้ทราบล่วงหน้า ไม่ Track ช่วงนอกเวลางาน','ปิด Tracking อัตโนมัติหลัง 18:00 น. และแจ้ง Policy ชัดเจน',5,4,
                ['Location ยานพาหนะ ชื่อคนขับ เวลา','no','no','no','no','500 คน','Legitimate Interest','yes','no','yes','no','no','no','4','yes','yes','yes','yes','Auto-disable นอกเวลางาน']],
            ['DPIA-2026-034','DPIA: ระบบบริหารสัญญาและเอกสาร','ระบบจัดการสัญญาก่อสร้างและคู่สัญญา','approved','low',12,
                'ความเสี่ยงต่ำ ข้อมูลส่วนใหญ่เป็นข้อมูลนิติบุคคล','ไม่มีมาตรการเพิ่มเติม',10,9,
                ['ชื่อ ผู้ติดต่อ ที่อยู่บริษัท','no','no','no','no','1,000 บริษัท','Contract','yes','no','yes','no','no','no','1','yes','yes','no','yes','ไม่มี']],
            ['DPIA-2026-035','DPIA: ระบบ Safety Incident Reporting','ระบบรายงานอุบัติเหตุในไซต์ก่อสร้าง','completed','medium',38,
                'ข้อมูลบาดเจ็บเป็นข้อมูลอ่อนไหว ต้องควบคุมการเข้าถึง','จำกัดสิทธิ์เข้าถึงเฉพาะ HR และ Safety Officer',4,3,
                ['ข้อมูลการบาดเจ็บ ชื่อพนักงาน รายละเอียดอุบัติเหตุ','yes','no','no','no','10,000 คน','Legal Obligation','yes','no','yes','yes','no','no','3','yes','yes','yes','yes','Role-based Access Control']],
            ['DPIA-2026-036','DPIA: ระบบขาย Condo และที่ดิน','ระบบ CRM สำหรับลูกค้าอสังหาฯ','approved','low',22,
                'ความเสี่ยงต่ำ มีการจัดการ Consent ที่ดี','ทบทวน Retention Period ให้ชัดเจน',6,5,
                ['ชื่อ เบอร์ อีเมล ความสนใจ งบประมาณ','no','no','no','no','50,000 ราย','Consent','yes','no','yes','no','no','no','2','yes','yes','yes','no','กำหนด Retention 3 ปี']],
            ['DPIA-2026-037','DPIA: ระบบตรวจสอบคุณวุฒิแรงงาน','Background Check พนักงานและ Subcontractor','completed','medium',50,
                'การตรวจสอบประวัติต้องมีความยินยอมชัดเจนจากบุคคล','ขอ Consent ก่อน Background Check ทุกครั้ง และลบข้อมูลหลัง 1 ปี',3,2,
                ['ประวัติอาชญากรรม วุฒิการศึกษา ประวัติการทำงาน','no','no','no','no','3,000 คน','Consent','yes','no','yes','yes','no','no','4','yes','yes','yes','yes','ลบข้อมูลหลังผ่านการ Verify']],
            ['DPIA-2026-038','DPIA: ระบบ BIM และข้อมูลอาคาร','Building Information Modeling ที่มีข้อมูลผู้พักอาศัย','in_progress','low',null,null,null,0,null,
                ['ข้อมูลอาคาร ผู้ครอบครอง ข้อมูลการใช้พลังงาน','no','no','no','no',null,'Contract',null,null,null,null,null,null,null,null,null,null,null,null]],

            // ── ราชการ / หน่วยงานรัฐ (39-50) ─────────────────────────────────
            ['DPIA-2026-039','DPIA: ระบบทะเบียนราษฎร์ออนไลน์','บริการทะเบียนราษฎร์ผ่านระบบออนไลน์','approved','high',72,
                'ข้อมูลทะเบียนราษฎร์เป็นข้อมูลพื้นฐานที่อ่อนไหว ต้องมีความปลอดภัยสูง','เพิ่ม Multi-Factor Authentication และ Audit Log ทุกการเข้าถึง',6,5,
                ['เลข 13 หลัก ชื่อ ที่อยู่ ข้อมูลครอบครัว','no','no','no','no','70,000,000 ราย','Legal Obligation','yes','no','yes','yes','no','no','7','yes','yes','yes','yes','HSM สำหรับจัดเก็บ Encryption Key']],
            ['DPIA-2026-040','DPIA: ระบบ e-Tax และการเสียภาษีออนไลน์','ระบบยื่นและชำระภาษีอิเล็กทรอนิกส์','approved','medium',52,
                'ข้อมูลภาษีเป็นข้อมูลที่ละเอียดอ่อน แต่มีฐานกฎหมายชัดเจน','เพิ่มการแจ้งเตือนผ่าน SMS เมื่อมีการเข้าถึงข้อมูล',8,7,
                ['เลขประจำตัวผู้เสียภาษี รายได้ ข้อมูลการเงิน','no','no','no','no','10,000,000 ราย','Legal Obligation','yes','no','yes','no','no','no','5','yes','yes','yes','yes','Activity Notification']],
            ['DPIA-2026-041','DPIA: ระบบการศึกษา — ข้อมูลนักเรียน','ระบบบริหารข้อมูลนักเรียนและผลการเรียน','approved','medium',42,
                'ข้อมูลเด็กต้องได้รับการคุ้มครองเป็นพิเศษ','ต้องขอ Consent จากผู้ปกครองสำหรับเด็กอายุต่ำกว่า 20 ปี',5,4,
                ['ผลการเรียน ข้อมูลสุขภาพ ข้อมูลครอบครัว','no','no','yes','no','5,000,000 คน','Legal Obligation','yes','no','yes','no','no','no','4','yes','yes','yes','yes','Parental Consent Process']],
            ['DPIA-2026-042','DPIA: ระบบ e-Court และข้อมูลคดี','ระบบจัดการคดีความทางศาล','in_progress','very_high',null,null,null,1,null,
                ['ข้อมูลคดี ประวัติผู้ต้องหา ข้อมูลพยาน','yes','no','no','no',null,'Legal Obligation',null,null,null,null,null,null,null,null,null,null,null,null]],
            ['DPIA-2026-043','DPIA: ระบบสาธารณสุข — ระบาดวิทยา','ระบบ Track and Trace โรคระบาด','completed','high',68,
                'การ Track ตำแหน่งเพื่อสาธารณสุขต้องมีขอบเขตที่ชัดเจน','กำหนด Sunset Clause เมื่อหมดภาวะฉุกเฉิน',4,3,
                ['ตำแหน่ง ข้อมูลสุขภาพ ประวัติการเดินทาง','yes','no','no','no','5,000,000 ราย','Legal Obligation','yes','no','yes','yes','no','no','6','yes','yes','yes','yes','Sunset Clause + Auto-delete']],
            ['DPIA-2026-044','DPIA: ระบบ CCTV สาธารณะ','กล้องวงจรปิดในพื้นที่สาธารณะของรัฐ','completed','high',65,
                'ต้องมีป้ายแจ้งเตือนและกำหนดระยะเวลาเก็บที่ชัดเจน','ติดป้ายแจ้งทุกจุดและกำหนดนโยบายการเก็บข้อมูล',6,5,
                ['ภาพวิดีโอสาธารณะ','no','no','no','no','ประชาชนทั่วไป','Legal Obligation','yes','no','yes','yes','no','no','6','yes','yes','yes','yes','ลบอัตโนมัติหลัง 30 วัน']],
            ['DPIA-2026-045','DPIA: ระบบ e-Passport และ Biometric','ระบบหนังสือเดินทางอิเล็กทรอนิกส์','approved','very_high',85,
                'Biometric Data ในหนังสือเดินทางมีความอ่อนไหวสูงมาก','ใช้ Hardware Security Module และมาตรฐาน ICAO','8','7',
                ['Biometric, ลายนิ้วมือ, ม่านตา, ใบหน้า','yes','no','no','yes','10,000,000 คน','Legal Obligation','yes','no','yes','yes','no','no','8','yes','yes','yes','yes','ICAO Standard + HSM']],
            ['DPIA-2026-046','DPIA: ระบบสวัสดิการแรงงาน','ระบบจัดการสวัสดิการและประกันสังคม','approved','medium',45,
                'ข้อมูลรายได้และสุขภาพต้องคุ้มครองเป็นพิเศษ','กำหนด Data Sharing Policy ที่ชัดเจนกับหน่วยงานที่เกี่ยวข้อง',9,8,
                ['รายได้ ข้อมูลการจ้างงาน ข้อมูลสุขภาพ','yes','no','no','no','20,000,000 คน','Legal Obligation','yes','no','yes','no','no','no','4','yes','yes','yes','yes','Data Sharing Agreement']],
            ['DPIA-2026-047','DPIA: ระบบ e-Procurement ภาครัฐ','ระบบจัดซื้อจัดจ้างอิเล็กทรอนิกส์','approved','low',20,
                'ความเสี่ยงต่ำ ข้อมูลส่วนใหญ่เป็นข้อมูลนิติบุคคล','ไม่มีมาตรการเพิ่มเติม',12,11,
                ['ชื่อผู้ประกอบการ เลขทะเบียน ข้อมูลบัญชี','no','no','no','no','100,000 บริษัท','Legal Obligation','yes','no','yes','no','no','no','1','yes','yes','yes','no','ไม่มี']],
            ['DPIA-2026-048','DPIA: ระบบ Smart City — IoT Sensors','เซ็นเซอร์ IoT ในเมืองอัจฉริยะ','in_progress','high',null,null,null,1,null,
                ['ตำแหน่ง การเดินทาง พฤติกรรมในพื้นที่สาธารณะ','no','yes','no','no',null,'Legal Obligation',null,null,null,null,null,null,null,null,null,null,null,null]],
            ['DPIA-2026-049','DPIA: ระบบ Open Data ภาครัฐ','การเปิดเผยข้อมูลสาธารณะของรัฐ','completed','low',15,
                'ผ่านการ Anonymize แล้ว ความเสี่ยงต่ำมาก','ทบทวน Re-identification Risk ทุก 6 เดือน',4,3,
                ['ข้อมูล Aggregated/Anonymized/Statistical','no','no','no','no','ข้อมูล Statistical','Legal Obligation','yes','no','yes','no','no','no','1','yes','yes','yes','yes','Re-identification Risk Assessment']],
            ['DPIA-2026-050','DPIA: ระบบ National ID Digital','บัตรประชาชนดิจิทัลและ Digital Identity','draft','very_high',null,null,null,0,null,
                ['เลข 13 หลัก Biometric ข้อมูลส่วนตัวทั้งหมด','yes','no','no','no',null,'Legal Obligation',null,null,null,null,null,null,null,null,null,null,null,null]],
        ];

        foreach ($dpias as $idx => $row) {
            [$num,$title,$scope,$status,$riskLevel,$riskScore,$findings,$recs,$startMo,$endMo,$answers] = $row;
            $startMo = is_numeric($startMo) ? (int)$startMo : 0;
            $endMo   = is_numeric($endMo)   ? (int)$endMo   : null;

            $meta = [
                'organization_id'   => 1,
                'assessment_number' => $num,
                'type'              => 'dpia',
                'title'             => $title,
                'scope'             => $scope,
                'status'            => $status,
                'risk_level'        => $riskLevel ?: null,
                'risk_score'        => $riskScore ?: null,
                'findings'          => $findings,
                'recommendations'   => $recs,
                'created_by'        => 2,
                'approved_by'       => in_array($status,['approved']) ? 1 : null,
                'started_at'        => $startMo ? now()->subMonths($startMo) : now()->subDays(3),
                'completed_at'      => $endMo ? now()->subMonths($endMo) : null,
                'approved_at'       => $status === 'approved' ? now()->subMonths(max(1,$endMo-1)) : null,
                'next_review_date'  => in_array($status,['completed','approved']) ? now()->addMonths(12) : null,
            ];

            $assessment = Assessment::create($meta);

            // Build answer map from row answers array
            // [dataType, sensitive, profiling, children, crossBorder, count, legalBasis, necessary, overCollect, retention, breach, discrim, dataQ, scale, encrypt, rbac, audit, breachPlan, mitigation]
            $a = $answers;

            foreach ($dpiaTemplate as $si => $section) {
                $sec = AssessmentSection::create([
                    'assessment_id' => $assessment->id,
                    'title'         => $section['title'],
                    'sort_order'    => $si + 1,
                ]);

                $qAnswers = match($si) {
                    0 => [$a[0], $a[1], $a[2], $a[3], $a[4], $a[5]],
                    1 => [$a[6], $a[7], $a[8], $a[9]],
                    2 => [$a[10], $a[11], $a[12], $a[13]],
                    3 => [$a[14], $a[15], $a[16], $a[17], $a[18]],
                    default => [],
                };

                foreach ($section['questions'] as $qi => $q) {
                    AssessmentQuestion::create([
                        'assessment_id' => $assessment->id,
                        'section_id'    => $sec->id,
                        'question'      => $q['question'],
                        'answer_type'   => $q['answer_type'],
                        'answer'        => $qAnswers[$qi] ?? null,
                        'risk_score'    => $q['risk_score'],
                        'sort_order'    => $qi + 1,
                    ]);
                }
            }
        }

        // LIA records (2)
        $this->seedLia();
        // Gap Analysis (1)
        $this->seedGap();

        $total = Assessment::count();
        $this->command->info("✅ Assessments seeded: {$total} total (50 DPIA + 2 LIA + 1 Gap Analysis) — all org 1");
    }

    private function seedLia(): void
    {
        $lias = [
            [
                'organization_id' => 1, 'assessment_number' => 'LIA-2026-001',
                'type' => 'lia', 'title' => 'LIA: การส่ง Newsletter รายสัปดาห์',
                'description' => 'ประเมินว่าสามารถใช้ Legitimate Interest เป็นฐานทางกฎหมายในการส่ง Newsletter ได้หรือไม่',
                'scope' => 'ลูกค้าที่เคยซื้อสินค้าใน 12 เดือนที่ผ่านมา',
                'status' => 'completed', 'risk_level' => 'low', 'risk_score' => 20,
                'findings' => 'Legitimate Interest ใช้ได้สำหรับลูกค้าเก่า แต่ต้องมี Opt-out ที่ชัดเจน',
                'recommendations' => 'เพิ่ม Unsubscribe link ทุก email และ Suppression List',
                'created_by' => 2, 'started_at' => now()->subMonths(3), 'completed_at' => now()->subMonths(2),
                'next_review_date' => now()->addYear(),
            ],
            [
                'organization_id' => 1, 'assessment_number' => 'LIA-2026-002',
                'type' => 'lia', 'title' => 'LIA: Fraud Detection — Transaction Monitoring',
                'description' => 'ประเมิน Legitimate Interest สำหรับการวิเคราะห์ Transaction เพื่อตรวจจับการทุจริต',
                'scope' => 'ระบบ Transaction Monitoring',
                'status' => 'draft', 'risk_level' => null, 'risk_score' => null,
                'created_by' => 2, 'started_at' => now()->subDays(1),
            ],
        ];
        foreach ($lias as $data) {
            Assessment::create($data);
        }
    }

    private function seedGap(): void
    {
        Assessment::create([
            'organization_id' => 1, 'assessment_number' => 'GAP-2026-001',
            'type' => 'gap_analysis', 'title' => 'Gap Analysis PDPA Compliance 2568',
            'description' => 'ประเมินความพร้อมด้าน PDPA Compliance ของบริษัทในปี 2568',
            'scope' => 'ทุกหน่วยงานในองค์กร',
            'status' => 'approved', 'risk_level' => 'medium', 'risk_score' => 45,
            'findings' => "พบช่องว่างหลัก 3 ด้าน:\n1) ROPA ยังไม่ครบทุก Process\n2) Vendor DPA บางรายหมดอายุ\n3) Training ยังไม่ครบทุกแผนก",
            'recommendations' => 'จัดทำแผน 90 วัน เพื่อแก้ไขช่องว่างที่พบ',
            'mitigation_measures' => 'ว่าจ้างที่ปรึกษา PDPA เพื่อตรวจสอบ Vendor DPA ทั้งหมด',
            'created_by' => 2, 'approved_by' => 1,
            'started_at' => now()->subMonths(1), 'completed_at' => now()->subWeeks(2),
            'approved_at' => now()->subWeeks(1), 'next_review_date' => now()->addMonths(6),
        ]);
    }
}
