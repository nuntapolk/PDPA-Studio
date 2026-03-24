<?php

namespace Database\Seeders;

use App\Models\TrainingCompletion;
use App\Models\TrainingCourse;
use App\Models\TrainingQuestion;
use App\Models\User;
use Illuminate\Database\Seeder;

class TrainingSeeder extends Seeder
{
    public function run(): void
    {
        TrainingCompletion::query()->forceDelete();
        TrainingQuestion::query()->forceDelete();
        TrainingCourse::query()->forceDelete();

        $orgId = 1;
        $user  = User::where('organization_id', $orgId)->first();

        $courses = [
            [
                'title'               => 'PDPA พื้นฐานสำหรับพนักงาน',
                'description'         => 'เรียนรู้หลักการสำคัญของ พ.ร.บ. คุ้มครองข้อมูลส่วนบุคคล พ.ศ. 2562 ที่พนักงานทุกคนต้องรู้',
                'content'             => '<h2>บทที่ 1: PDPA คืออะไร?</h2>
<p>พระราชบัญญัติคุ้มครองข้อมูลส่วนบุคคล พ.ศ. 2562 (PDPA) เป็นกฎหมายที่กำหนดหลักเกณฑ์เกี่ยวกับการเก็บรวบรวม ใช้ หรือเปิดเผยข้อมูลส่วนบุคคล</p>
<h2>บทที่ 2: ข้อมูลส่วนบุคคลคืออะไร?</h2>
<p>ข้อมูลส่วนบุคคล คือ ข้อมูลที่สามารถระบุตัวตนบุคคลได้ ทั้งทางตรงและทางอ้อม เช่น ชื่อ นามสกุล เลขบัตรประชาชน หมายเลขโทรศัพท์ อีเมล ที่อยู่</p>
<h2>บทที่ 3: หน้าที่ของพนักงาน</h2>
<ul>
<li>เก็บข้อมูลเท่าที่จำเป็น</li>
<li>ไม่นำข้อมูลไปใช้นอกเหนือวัตถุประสงค์</li>
<li>รักษาความปลอดภัยของข้อมูล</li>
<li>แจ้งเหตุการณ์ละเมิดทันที</li>
</ul>',
                'duration_minutes'    => 30,
                'is_required'         => true,
                'passing_score'       => 70,
                'certificate_enabled' => true,
                'validity_months'     => 12,
                'is_active'           => true,
                'questions' => [
                    ['question' => 'PDPA ย่อมาจากอะไร?',
                     'options'  => ['A'=>'Personal Data Protection Act','B'=>'Private Data Privacy Agreement','C'=>'Public Data Processing Authority','D'=>'Personal Digital Privacy Act'],
                     'correct'  => 'A', 'explanation' => 'PDPA ย่อมาจาก Personal Data Protection Act หรือ พ.ร.บ. คุ้มครองข้อมูลส่วนบุคคล'],
                    ['question' => 'ข้อใดถือเป็นข้อมูลส่วนบุคคล?',
                     'options'  => ['A'=>'ราคาสินค้า','B'=>'เลขบัตรประชาชน','C'=>'ชื่อบริษัท','D'=>'ตราสินค้า'],
                     'correct'  => 'B', 'explanation' => 'เลขบัตรประชาชนเป็นข้อมูลที่สามารถระบุตัวตนได้โดยตรง'],
                    ['question' => 'หากพบการรั่วไหลของข้อมูล ควรแจ้ง PDPC ภายในกี่ชั่วโมง?',
                     'options'  => ['A'=>'24 ชั่วโมง','B'=>'48 ชั่วโมง','C'=>'72 ชั่วโมง','D'=>'96 ชั่วโมง'],
                     'correct'  => 'C', 'explanation' => 'กฎหมาย PDPA กำหนดให้แจ้งภายใน 72 ชั่วโมงนับจากทราบเหตุการณ์'],
                    ['question' => 'ข้อใดเป็นข้อมูลส่วนบุคคลอ่อนไหว?',
                     'options'  => ['A'=>'ชื่อ-นามสกุล','B'=>'ข้อมูลสุขภาพ','C'=>'ที่อยู่ที่ทำงาน','D'=>'อีเมลธุรกิจ'],
                     'correct'  => 'B', 'explanation' => 'ข้อมูลสุขภาพจัดเป็น Sensitive Data ที่ต้องได้รับความยินยอมโดยชัดแจ้ง'],
                    ['question' => 'ฐานทางกฎหมายใดไม่ใช่ฐานในการประมวลผลข้อมูลตาม PDPA?',
                     'options'  => ['A'=>'ความยินยอม','B'=>'สัญญา','C'=>'ผลประโยชน์ส่วนตัว','D'=>'ประโยชน์สาธารณะ'],
                     'correct'  => 'C', 'explanation' => 'ผลประโยชน์ส่วนตัวไม่ใช่ฐานทางกฎหมายที่ PDPA รับรอง'],
                ],
            ],
            [
                'title'               => 'การจัดการความยินยอม (Consent Management)',
                'description'         => 'เข้าใจการขอ จัดเก็บ และถอนความยินยอมอย่างถูกต้องตามกฎหมาย PDPA',
                'content'             => '<h2>ความยินยอมที่ถูกต้องตาม PDPA</h2>
<p>ความยินยอม (Consent) ต้องเป็นไปอย่างอิสระ เจาะจง ชัดเจน และรับรู้ล่วงหน้า โดยเจ้าของข้อมูลต้องให้ความยินยอมอย่างแสดงออกชัดเจน</p>
<h2>องค์ประกอบของความยินยอม</h2>
<ul>
<li><strong>อิสระ:</strong> ไม่มีการบังคับ</li>
<li><strong>เจาะจง:</strong> ระบุวัตถุประสงค์ชัดเจน</li>
<li><strong>รับรู้:</strong> ให้ข้อมูลครบถ้วน</li>
<li><strong>แสดงออก:</strong> ไม่ใช้การกระทำโดยปริยาย</li>
</ul>
<h2>การถอนความยินยอม</h2>
<p>เจ้าของข้อมูลมีสิทธิถอนความยินยอมได้ตลอดเวลา และต้องทำได้ง่ายเท่ากับการให้ความยินยอม</p>',
                'duration_minutes'    => 45,
                'is_required'         => true,
                'passing_score'       => 70,
                'certificate_enabled' => true,
                'validity_months'     => 12,
                'is_active'           => true,
                'questions' => [
                    ['question' => 'ข้อใดเป็นลักษณะของความยินยอมที่ถูกต้อง?',
                     'options'  => ['A'=>'ใช้ช่องกาเครื่องหมายที่ติ๊กไว้ล่วงหน้า','B'=>'ระบุวัตถุประสงค์ชัดเจนและให้อิสระ','C'=>'รวมเงื่อนไขในข้อกำหนดการใช้งาน','D'=>'ส่งอีเมลแจ้งเท่านั้น'],
                     'correct'  => 'B', 'explanation' => 'ความยินยอมต้องอิสระ เจาะจง รับรู้ล่วงหน้า และแสดงออกชัดเจน'],
                    ['question' => 'เมื่อเจ้าของข้อมูลถอนความยินยอม ควรทำอย่างไร?',
                     'options'  => ['A'=>'หยุดประมวลผลทันทีทุกกรณี','B'=>'หยุดประมวลผลที่ไม่มีฐานกฎหมายอื่น','C'=>'แจ้งให้ทราบและดำเนินการต่อ','D'=>'รอ 30 วันก่อนดำเนินการ'],
                     'correct'  => 'B', 'explanation' => 'หยุดประมวลผลที่อาศัยความยินยอม แต่ยังประมวลผลได้หากมีฐานทางกฎหมายอื่น'],
                    ['question' => 'อายุขั้นต่ำในการให้ความยินยอมด้วยตนเองตาม PDPA?',
                     'options'  => ['A'=>'10 ปี','B'=>'15 ปี','C'=>'18 ปี','D'=>'20 ปี'],
                     'correct'  => 'C', 'explanation' => 'ผู้เยาว์อายุต่ำกว่า 20 ปีต้องได้รับความยินยอมจากผู้ปกครอง'],
                    ['question' => 'Privacy Notice ควรมีข้อมูลใด?',
                     'options'  => ['A'=>'ชื่อผู้ควบคุมข้อมูลและวัตถุประสงค์','B'=>'ราคาค่าบริการ','C'=>'ข้อมูลคู่แข่ง','D'=>'ประวัติบริษัท'],
                     'correct'  => 'A', 'explanation' => 'Privacy Notice ต้องระบุตัวผู้ควบคุมข้อมูล วัตถุประสงค์ ระยะเวลา และสิทธิของเจ้าของข้อมูล'],
                    ['question' => 'Opt-in และ Opt-out แตกต่างกันอย่างไร?',
                     'options'  => ['A'=>'ไม่แตกต่าง','B'=>'Opt-in คือเลือกเข้า Opt-out คือเลือกออก','C'=>'Opt-out คือเลือกเข้า Opt-in คือเลือกออก','D'=>'ใช้แทนกันได้'],
                     'correct'  => 'B', 'explanation' => 'Opt-in ต้องให้ผู้ใช้กระทำการยืนยัน ส่วน Opt-out คือยกเลิกหลังจากถูกเพิ่มโดยอัตโนมัติ'],
                ],
            ],
            [
                'title'               => 'สิทธิของเจ้าของข้อมูลส่วนบุคคล',
                'description'         => 'ทำความเข้าใจสิทธิ 8 ประการของเจ้าของข้อมูลและวิธีรับมือคำขอ',
                'content'             => '<h2>สิทธิของเจ้าของข้อมูล 8 ประการ</h2>
<ol>
<li><strong>สิทธิรับรู้:</strong> ได้รับแจ้งเมื่อข้อมูลถูกเก็บรวบรวม</li>
<li><strong>สิทธิเข้าถึง:</strong> ขอสำเนาข้อมูลของตนเอง</li>
<li><strong>สิทธิแก้ไข:</strong> แก้ไขข้อมูลที่ไม่ถูกต้อง</li>
<li><strong>สิทธิลบ:</strong> ขอให้ลบข้อมูล (Right to be Forgotten)</li>
<li><strong>สิทธิระงับ:</strong> ระงับการใช้ข้อมูลชั่วคราว</li>
<li><strong>สิทธิโอน:</strong> รับข้อมูลในรูปแบบที่อ่านได้</li>
<li><strong>สิทธิคัดค้าน:</strong> คัดค้านการประมวลผล</li>
<li><strong>สิทธิถอนความยินยอม:</strong> ถอนความยินยอมที่ให้ไว้</li>
</ol>
<h2>ระยะเวลาตอบสนอง</h2>
<p>ต้องตอบสนองคำร้องภายใน 30 วันนับจากได้รับคำขอ หากต้องการเวลาเพิ่ม ต้องแจ้งเหตุผล</p>',
                'duration_minutes'    => 40,
                'is_required'         => true,
                'passing_score'       => 70,
                'certificate_enabled' => true,
                'validity_months'     => 12,
                'is_active'           => true,
                'questions' => [
                    ['question' => 'เจ้าของข้อมูลมีสิทธิกี่ประการตาม PDPA?',
                     'options'  => ['A'=>'5 ประการ','B'=>'6 ประการ','C'=>'8 ประการ','D'=>'10 ประการ'],
                     'correct'  => 'C', 'explanation' => 'PDPA กำหนดสิทธิของเจ้าของข้อมูล 8 ประการ'],
                    ['question' => 'Right to be Forgotten คือสิทธิใด?',
                     'options'  => ['A'=>'สิทธิเข้าถึง','B'=>'สิทธิลบข้อมูล','C'=>'สิทธิโอนข้อมูล','D'=>'สิทธิคัดค้าน'],
                     'correct'  => 'B', 'explanation' => 'Right to be Forgotten คือสิทธิในการลบหรือทำลายข้อมูล'],
                    ['question' => 'ต้องตอบสนองคำร้องสิทธิภายในกี่วัน?',
                     'options'  => ['A'=>'7 วัน','B'=>'14 วัน','C'=>'30 วัน','D'=>'60 วัน'],
                     'correct'  => 'C', 'explanation' => 'ต้องดำเนินการและแจ้งผลภายใน 30 วันนับจากได้รับคำขอ'],
                    ['question' => 'สิทธิใดช่วยให้เจ้าของข้อมูลนำข้อมูลไปใช้กับผู้ให้บริการอื่น?',
                     'options'  => ['A'=>'สิทธิเข้าถึง','B'=>'สิทธิแก้ไข','C'=>'สิทธิโอนข้อมูล','D'=>'สิทธิระงับ'],
                     'correct'  => 'C', 'explanation' => 'Data Portability หรือสิทธิโอนข้อมูล ช่วยให้นำข้อมูลไปใช้กับระบบอื่น'],
                    ['question' => 'เมื่อได้รับคำร้องขอลบข้อมูล ควรทำอะไรก่อน?',
                     'options'  => ['A'=>'ลบทันทีโดยไม่ตรวจสอบ','B'=>'ปฏิเสธทุกกรณี','C'=>'ตรวจสอบว่ามีฐานกฎหมายที่ต้องเก็บไว้หรือไม่','D'=>'รอให้เจ้าของข้อมูลยื่นเรื่องใหม่'],
                     'correct'  => 'C', 'explanation' => 'ต้องตรวจสอบก่อนว่ามีฐานทางกฎหมายอื่นที่ต้องเก็บข้อมูลไว้หรือไม่'],
                ],
            ],
            [
                'title'               => 'ความปลอดภัยของข้อมูลและการรายงานเหตุการณ์',
                'description'         => 'มาตรการรักษาความปลอดภัยข้อมูลและขั้นตอนการรายงานเมื่อเกิด Data Breach',
                'content'             => '<h2>มาตรการรักษาความปลอดภัย</h2>
<p>ผู้ควบคุมข้อมูลต้องจัดให้มีมาตรการรักษาความปลอดภัยที่เหมาะสม ทั้งด้านเทคนิคและองค์กร</p>
<h2>มาตรการด้านเทคนิค</h2>
<ul>
<li>การเข้ารหัสข้อมูล (Encryption)</li>
<li>การควบคุมสิทธิ์เข้าถึง (Access Control)</li>
<li>การสำรองข้อมูล (Backup)</li>
<li>การบันทึกรายการ (Audit Log)</li>
</ul>
<h2>ขั้นตอนเมื่อเกิด Data Breach</h2>
<ol>
<li>ระบุและควบคุมการรั่วไหล</li>
<li>ประเมินความเสี่ยง</li>
<li>แจ้ง PDPC ภายใน 72 ชั่วโมง</li>
<li>แจ้งเจ้าของข้อมูลหากความเสี่ยงสูง</li>
<li>บันทึกเหตุการณ์</li>
</ol>',
                'duration_minutes'    => 35,
                'is_required'         => false,
                'passing_score'       => 70,
                'certificate_enabled' => true,
                'validity_months'     => 12,
                'is_active'           => true,
                'questions' => [
                    ['question' => 'มาตรการใดช่วยป้องกันการเข้าถึงข้อมูลโดยไม่ได้รับอนุญาต?',
                     'options'  => ['A'=>'Encryption และ Access Control','B'=>'การใช้รหัสผ่านเดิม','C'=>'การแชร์ข้อมูลทั่วไป','D'=>'การปิดระบบ Firewall'],
                     'correct'  => 'A', 'explanation' => 'Encryption ป้องกันการอ่านข้อมูล ส่วน Access Control จำกัดผู้ที่เข้าถึงได้'],
                    ['question' => 'เมื่อพบ Data Breach ต้องแจ้ง PDPC ภายในกี่ชั่วโมง?',
                     'options'  => ['A'=>'24 ชั่วโมง','B'=>'48 ชั่วโมง','C'=>'72 ชั่วโมง','D'=>'1 สัปดาห์'],
                     'correct'  => 'C', 'explanation' => 'กฎหมายกำหนดให้แจ้งภายใน 72 ชั่วโมงนับจากทราบเหตุ'],
                    ['question' => 'Audit Log มีประโยชน์อย่างไร?',
                     'options'  => ['A'=>'เพิ่มความเร็วระบบ','B'=>'บันทึกการเข้าถึงและการกระทำในระบบ','C'=>'ลดค่าใช้จ่าย','D'=>'เปลี่ยนรหัสผ่านอัตโนมัติ'],
                     'correct'  => 'B', 'explanation' => 'Audit Log ช่วยตรวจสอบว่าใครเข้าถึงข้อมูลอะไรและเมื่อไร'],
                    ['question' => 'ข้อมูลใดไม่ควรส่งผ่านอีเมลทั่วไป?',
                     'options'  => ['A'=>'ชื่อผลิตภัณฑ์','B'=>'ตารางเวลาประชุม','C'=>'เลขบัตรประชาชนของลูกค้า','D'=>'ลิงก์ข่าวสาร'],
                     'correct'  => 'C', 'explanation' => 'ข้อมูลส่วนบุคคลอ่อนไหวไม่ควรส่งผ่านช่องทางที่ไม่ปลอดภัย'],
                    ['question' => 'การสำรองข้อมูล (Backup) ช่วยอะไร?',
                     'options'  => ['A'=>'ป้องกันการเข้าถึงโดยไม่ได้รับอนุญาต','B'=>'กู้คืนข้อมูลเมื่อเกิดความเสียหาย','C'=>'เพิ่มความเร็วการประมวลผล','D'=>'ลดปริมาณข้อมูล'],
                     'correct'  => 'B', 'explanation' => 'Backup ช่วยให้กู้คืนข้อมูลได้เมื่อเกิดเหตุการณ์ไม่คาดคิด'],
                ],
            ],
            [
                'title'               => 'DPIA และการประเมินความเสี่ยงความเป็นส่วนตัว',
                'description'         => 'เรียนรู้การทำ Data Protection Impact Assessment สำหรับโครงการที่มีความเสี่ยงสูง',
                'content'             => '<h2>DPIA คืออะไร?</h2>
<p>Data Protection Impact Assessment (DPIA) คือกระบวนการประเมินผลกระทบด้านการคุ้มครองข้อมูลส่วนบุคคล สำหรับกิจกรรมที่อาจก่อให้เกิดความเสี่ยงสูง</p>
<h2>เมื่อไรต้องทำ DPIA?</h2>
<ul>
<li>การใช้เทคโนโลยีใหม่ที่มีผลกระทบสูง</li>
<li>การประมวลผล Sensitive Data ขนาดใหญ่</li>
<li>การ Profiling อย่างเป็นระบบ</li>
<li>การเฝ้าระวังในพื้นที่สาธารณะ</li>
</ul>
<h2>ขั้นตอน DPIA</h2>
<ol>
<li>ระบุกิจกรรมและวัตถุประสงค์</li>
<li>ประเมินความจำเป็นและสัดส่วน</li>
<li>ระบุและประเมินความเสี่ยง</li>
<li>กำหนดมาตรการลดความเสี่ยง</li>
<li>บันทึกและทบทวน</li>
</ol>',
                'duration_minutes'    => 60,
                'is_required'         => false,
                'passing_score'       => 80,
                'certificate_enabled' => true,
                'validity_months'     => 24,
                'is_active'           => true,
                'questions' => [
                    ['question' => 'DPIA ย่อมาจากอะไร?',
                     'options'  => ['A'=>'Data Privacy Impact Assessment','B'=>'Data Protection Impact Assessment','C'=>'Digital Privacy Information Act','D'=>'Data Processing Internal Audit'],
                     'correct'  => 'B', 'explanation' => 'DPIA ย่อมาจาก Data Protection Impact Assessment'],
                    ['question' => 'กรณีใดต้องทำ DPIA?',
                     'options'  => ['A'=>'เก็บข้อมูลพนักงาน 5 คน','B'=>'ส่งอีเมลข่าวสารรายเดือน','C'=>'ระบบ AI ที่ตัดสินใจอัตโนมัติผลกระทบสูง','D'=>'บันทึกชื่อผู้เข้าประชุม'],
                     'correct'  => 'C', 'explanation' => 'ระบบ AI ที่ตัดสินใจอัตโนมัติและมีผลกระทบสูงต้องทำ DPIA'],
                    ['question' => 'ขั้นตอนแรกของ DPIA คืออะไร?',
                     'options'  => ['A'=>'กำหนดมาตรการรักษาความปลอดภัย','B'=>'ระบุกิจกรรมและวัตถุประสงค์','C'=>'แจ้ง PDPC','D'=>'ขอความยินยอมใหม่'],
                     'correct'  => 'B', 'explanation' => 'ขั้นตอนแรกคือการระบุว่ากิจกรรมคืออะไรและมีวัตถุประสงค์อะไร'],
                    ['question' => 'ผู้ใดควรมีส่วนร่วมในกระบวนการ DPIA?',
                     'options'  => ['A'=>'ฝ่าย IT เท่านั้น','B'=>'DPO กฎหมาย และเจ้าของโครงการ','C'=>'ลูกค้าเท่านั้น','D'=>'PDPC'],
                     'correct'  => 'B', 'explanation' => 'DPIA ควรเป็นความร่วมมือระหว่าง DPO ทีมกฎหมาย และเจ้าของโครงการ'],
                    ['question' => 'หลังทำ DPIA แล้วพบความเสี่ยงสูงมาก ควรทำอย่างไร?',
                     'options'  => ['A'=>'ดำเนินโครงการต่อโดยไม่แก้ไข','B'=>'ปรึกษา PDPC ก่อนดำเนินการ','C'=>'ยกเลิกโครงการทันที','D'=>'รอให้กฎหมายเปลี่ยน'],
                     'correct'  => 'B', 'explanation' => 'เมื่อความเสี่ยงสูงและลดไม่ได้ ต้องปรึกษา PDPC ก่อน (Prior Consultation)'],
                ],
            ],
        ];

        foreach ($courses as $i => $courseData) {
            $questions = $courseData['questions'];
            unset($courseData['questions']);

            $course = TrainingCourse::create(array_merge($courseData, [
                'organization_id' => $orgId,
                'created_by'      => $user?->id ?? 1,
            ]));

            foreach ($questions as $j => $q) {
                TrainingQuestion::create([
                    'course_id'      => $course->id,
                    'question'       => $q['question'],
                    'options'        => $q['options'],
                    'correct_answer' => $q['correct'],
                    'explanation'    => $q['explanation'] ?? null,
                    'sort_order'     => $j + 1,
                ]);
            }

            // Create some completions for each course
            $users = User::where('organization_id', $orgId)->get();
            foreach ($users->take(min(count($users), rand(2,5))) as $u) {
                $passed = (bool)rand(0,1);
                $score  = $passed ? rand($courseData['passing_score'], 100) : rand(30, $courseData['passing_score']-1);
                $attempt = 1;

                $certNumber = null;
                $expiresAt  = null;
                if ($passed && $courseData['certificate_enabled']) {
                    $certNumber = TrainingCompletion::generateCertNumber($course->id, $u->id);
                    $expiresAt  = now()->addMonths($courseData['validity_months']);
                }

                TrainingCompletion::create([
                    'course_id'          => $course->id,
                    'user_id'            => $u->id,
                    'score'              => $score,
                    'passed'             => $passed,
                    'attempt_number'     => $attempt,
                    'certificate_number' => $certNumber,
                    'started_at'         => now()->subDays(rand(1,60))->subMinutes(rand(20,60)),
                    'completed_at'       => now()->subDays(rand(1,60)),
                    'expires_at'         => $expiresAt,
                ]);
            }
        }

        $this->command->info('TrainingSeeder: '.TrainingCourse::count().' courses, '.TrainingQuestion::count().' questions, '.TrainingCompletion::count().' completions');
    }
}
