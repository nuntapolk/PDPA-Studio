<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentQuestion;
use App\Models\AssessmentSection;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssessmentController extends Controller
{
    public function index(Request $request)
    {
        $orgId = Auth::user()->organization_id;

        $query = Assessment::where('organization_id', $orgId);

        if ($request->filled('type'))   $query->where('type', $request->type);
        if ($request->filled('status')) $query->where('status', $request->status);
        if ($request->filled('risk'))   $query->where('risk_level', $request->risk);
        if ($request->filled('search')) {
            $query->where(fn($q) => $q->where('title', 'like', '%'.$request->search.'%')
                ->orWhere('assessment_number', 'like', '%'.$request->search.'%'));
        }

        $perPage = in_array((int)$request->per_page, [50, 100, 200]) ? (int)$request->per_page : 50;
        $assessments = $query->latest()->paginate($perPage)->withQueryString();

        $base          = Assessment::where('organization_id', $orgId);
        $totalCount    = (clone $base)->count();
        $highRiskCount = (clone $base)->whereIn('risk_level', ['high','very_high'])->count();
        $pendingCount  = (clone $base)->whereIn('status', ['draft','in_progress'])->count();
        $completedCount= (clone $base)->whereIn('status', ['completed','approved'])->count();

        return view('modules.assessment.index', compact(
            'assessments','totalCount','highRiskCount','pendingCount','completedCount'
        ));
    }

    public function create()
    {
        $dpiaQuestions = $this->dpiaTemplate();
        $liaQuestions  = $this->liaTemplate();
        $gapQuestions  = $this->gapTemplate();
        return view('modules.assessment.create', compact('dpiaQuestions','liaQuestions','gapQuestions'));
    }

    public function store(Request $request)
    {
        $orgId = Auth::user()->organization_id;

        $validated = $request->validate([
            'type'        => 'required|in:dpia,lia,gap_analysis',
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'scope'       => 'nullable|string|max:500',
        ]);

        $assessment = Assessment::create(array_merge($validated, [
            'organization_id' => $orgId,
            'created_by'      => Auth::id(),
            'status'          => 'in_progress',
            'started_at'      => now(),
        ]));

        // Create sections & questions from template
        $sections = $this->getSectionsForType($request->type);
        foreach ($sections as $si => $section) {
            $sec = AssessmentSection::create([
                'assessment_id' => $assessment->id,
                'title'         => $section['title'],
                'sort_order'    => $si + 1,
            ]);
            foreach ($section['questions'] as $qi => $q) {
                AssessmentQuestion::create([
                    'assessment_id' => $assessment->id,
                    'section_id'    => $sec->id,
                    'question'      => $q['question'],
                    'answer_type'   => $q['answer_type'],
                    'risk_score'    => $q['risk_weight'] ?? 0,
                    'sort_order'    => $qi + 1,
                    'options'       => $q['options'] ?? null,
                ]);
            }
        }

        AuditLog::record('created', 'assessment', $assessment);

        return redirect()->route('assessment.show', $assessment)
            ->with('success', "สร้าง {$assessment->assessment_number} เรียบร้อย — กรุณาตอบคำถามเพื่อประเมิน");
    }

    public function show(Assessment $assessment)
    {
        $this->authorizeOrg($assessment->organization_id);

        $sections  = $assessment->sections()->orderBy('sort_order')
            ->with(['questions' => fn($q) => $q->orderBy('sort_order')])
            ->get();
        $questions = $assessment->questions()->whereNull('section_id')->orderBy('sort_order')->get();

        AuditLog::record('viewed', 'assessment', $assessment);

        return view('modules.assessment.show', compact('assessment','sections','questions'));
    }

    public function edit(Assessment $assessment)
    {
        $this->authorizeOrg($assessment->organization_id);
        return view('modules.assessment.edit', compact('assessment'));
    }

    public function update(Request $request, Assessment $assessment)
    {
        $this->authorizeOrg($assessment->organization_id);

        $validated = $request->validate([
            'title'               => 'required|string|max:255',
            'description'         => 'nullable|string',
            'scope'               => 'nullable|string|max:500',
            'status'              => 'required|in:draft,in_progress,completed,approved,archived',
            'risk_level'          => 'nullable|in:low,medium,high,very_high',
            'risk_score'          => 'nullable|integer|min:0|max:100',
            'findings'            => 'nullable|string',
            'recommendations'     => 'nullable|string',
            'mitigation_measures' => 'nullable|string',
            'started_at'          => 'nullable|date',
            'completed_at'        => 'nullable|date',
            'next_review_date'    => 'nullable|date',
        ]);

        if ($validated['status'] === 'completed' && !$assessment->completed_at && empty($validated['completed_at'])) {
            $validated['completed_at'] = now();
        }

        $assessment->update($validated);
        AuditLog::record('updated', 'assessment', $assessment);

        return redirect()->route('assessment.show', $assessment)->with('success', 'อัปเดตการประเมินเรียบร้อย');
    }

    /** บันทึกคำตอบของ Question เดียว (AJAX-friendly POST) */
    public function answerQuestion(Request $request, Assessment $assessment, AssessmentQuestion $question)
    {
        $this->authorizeOrg($assessment->organization_id);

        $request->validate(['answer' => 'nullable|string', 'notes' => 'nullable|string']);

        $question->update([
            'answer' => $request->answer,
            'notes'  => $request->notes,
        ]);

        // Recalculate risk score from answered questions
        $this->recalculateRisk($assessment);

        return back()->with('success', 'บันทึกคำตอบแล้ว');
    }

    /** บันทึกทุกคำตอบพร้อมกัน (bulk save) */
    public function saveAnswers(Request $request, Assessment $assessment)
    {
        $this->authorizeOrg($assessment->organization_id);

        $answers = $request->input('answers', []);
        $notes   = $request->input('notes', []);

        foreach ($answers as $qid => $answer) {
            AssessmentQuestion::where('id', $qid)
                ->where('assessment_id', $assessment->id)
                ->update(['answer' => $answer, 'notes' => $notes[$qid] ?? null]);
        }

        $this->recalculateRisk($assessment);

        return back()->with('success', 'บันทึกคำตอบทั้งหมดแล้ว');
    }

    public function approve(Assessment $assessment)
    {
        $this->authorizeOrg($assessment->organization_id);

        $assessment->update([
            'status'      => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        AuditLog::record('approved', 'assessment', $assessment);

        return back()->with('success', 'อนุมัติการประเมินเรียบร้อย');
    }

    public function export(Assessment $assessment)
    {
        $this->authorizeOrg($assessment->organization_id);

        $sections = $assessment->sections()->orderBy('sort_order')
            ->with(['questions' => fn($q) => $q->orderBy('sort_order')])
            ->get();

        AuditLog::record('exported', 'assessment', $assessment);

        return response()->streamDownload(function () use ($assessment, $sections) {
            $h = fopen('php://output', 'w');
            fputs($h, "\xEF\xBB\xBF");
            fputcsv($h, ['DPIA/Assessment Report — ' . $assessment->assessment_number]);
            fputcsv($h, ['ชื่อ', $assessment->title]);
            fputcsv($h, ['ประเภท', $assessment->getTypeLabel()]);
            fputcsv($h, ['สถานะ', $assessment->status]);
            fputcsv($h, ['ระดับความเสี่ยง', $assessment->risk_level ?? '']);
            fputcsv($h, ['คะแนน', $assessment->risk_score ?? '']);
            fputcsv($h, ['ขอบเขต', $assessment->scope ?? '']);
            fputcsv($h, []);
            fputcsv($h, ['หัวข้อ','คำถาม','ประเภทคำตอบ','คำตอบ','หมายเหตุ','น้ำหนักความเสี่ยง']);
            foreach ($sections as $sec) {
                foreach ($sec->questions as $q) {
                    fputcsv($h, [$sec->title, $q->question, $q->answer_type, $q->answer ?? '', $q->notes ?? '', $q->risk_score]);
                }
            }
            fputcsv($h, []);
            fputcsv($h, ['สิ่งที่พบ', $assessment->findings ?? '']);
            fputcsv($h, ['ข้อเสนอแนะ', $assessment->recommendations ?? '']);
            fputcsv($h, ['มาตรการลดความเสี่ยง', $assessment->mitigation_measures ?? '']);
            fclose($h);
        }, 'assessment-'.$assessment->assessment_number.'-'.now()->format('Ymd').'.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    // ────────────────────────────────────────────────
    private function recalculateRisk(Assessment $assessment): void
    {
        $questions = $assessment->questions()->whereNotNull('answer')->get();
        if ($questions->isEmpty()) return;

        $totalWeight = $questions->sum('risk_score');
        $maxWeight   = $assessment->questions()->sum('risk_score') ?: 1;

        // Yes answers on yes_no questions add their weight; scale answers scale the weight
        $earnedScore = 0;
        foreach ($questions as $q) {
            if ($q->answer_type === 'yes_no' && $q->answer === 'yes') {
                $earnedScore += $q->risk_score;
            } elseif ($q->answer_type === 'scale') {
                $scale = min(10, max(0, (int) $q->answer));
                $earnedScore += ($scale / 10) * $q->risk_score;
            }
        }

        $score = $maxWeight > 0 ? round(($earnedScore / $maxWeight) * 100) : 0;
        $level = match(true) {
            $score <= 30 => 'low',
            $score <= 60 => 'medium',
            $score <= 80 => 'high',
            default      => 'very_high',
        };

        $assessment->update(['risk_score' => $score, 'risk_level' => $level]);
    }

    private function authorizeOrg(int $orgId): void
    {
        if (Auth::user()->organization_id !== $orgId) abort(403);
    }

    // ── Question Templates ────────────────────────────────────────────────────
    private function getSectionsForType(string $type): array
    {
        return match($type) {
            'dpia'         => $this->dpiaTemplate(),
            'lia'          => $this->liaTemplate(),
            'gap_analysis' => $this->gapTemplate(),
            default        => [],
        };
    }

    private function dpiaTemplate(): array
    {
        return [
            ['title' => '1. ข้อมูลระบบและการประมวลผล', 'questions' => [
                ['question' => 'ระบบ/กระบวนการนี้ประมวลผลข้อมูลส่วนบุคคลประเภทใด?', 'answer_type' => 'text', 'risk_weight' => 0],
                ['question' => 'มีการประมวลผลข้อมูลอ่อนไหว (มาตรา 26) หรือไม่? (สุขภาพ, ชีวมิติ, พันธุกรรม ฯลฯ)', 'answer_type' => 'yes_no', 'risk_weight' => 15],
                ['question' => 'มีการทำ Profiling หรือ Automated Decision Making หรือไม่?', 'answer_type' => 'yes_no', 'risk_weight' => 12],
                ['question' => 'มีการประมวลผลข้อมูลของเด็กอายุต่ำกว่า 20 ปี หรือไม่?', 'answer_type' => 'yes_no', 'risk_weight' => 10],
                ['question' => 'มีการส่งข้อมูลไปต่างประเทศหรือไม่?', 'answer_type' => 'yes_no', 'risk_weight' => 8],
                ['question' => 'จำนวนเจ้าของข้อมูลที่ได้รับผลกระทบ (ประมาณ)', 'answer_type' => 'text', 'risk_weight' => 0],
            ]],
            ['title' => '2. ฐานทางกฎหมายและความจำเป็น', 'questions' => [
                ['question' => 'ฐานทางกฎหมายที่ใช้สำหรับการประมวลผลคืออะไร?', 'answer_type' => 'text', 'risk_weight' => 0],
                ['question' => 'การประมวลผลมีความจำเป็นและได้สัดส่วนกับวัตถุประสงค์หรือไม่?', 'answer_type' => 'yes_no', 'risk_weight' => 8],
                ['question' => 'มีการเก็บข้อมูลมากกว่าที่จำเป็น (Over-collection) หรือไม่?', 'answer_type' => 'yes_no', 'risk_weight' => 10],
                ['question' => 'ระยะเวลาเก็บรักษาข้อมูลมีความเหมาะสมหรือไม่?', 'answer_type' => 'yes_no', 'risk_weight' => 6],
            ]],
            ['title' => '3. การระบุและประเมินความเสี่ยง', 'questions' => [
                ['question' => 'มีความเสี่ยงด้าน Unauthorized Access หรือ Data Breach หรือไม่?', 'answer_type' => 'yes_no', 'risk_weight' => 12],
                ['question' => 'มีความเสี่ยงที่เจ้าของข้อมูลจะถูกเลือกปฏิบัติ (Discrimination) หรือไม่?', 'answer_type' => 'yes_no', 'risk_weight' => 10],
                ['question' => 'มีความเสี่ยงด้านความถูกต้องของข้อมูล (Data Quality) หรือไม่?', 'answer_type' => 'yes_no', 'risk_weight' => 6],
                ['question' => 'ระดับความเสี่ยงโดยรวมที่ประเมินได้ (0=ต่ำ, 10=สูงมาก)', 'answer_type' => 'scale', 'risk_weight' => 15],
            ]],
            ['title' => '4. มาตรการลดความเสี่ยง', 'questions' => [
                ['question' => 'มีมาตรการ Encryption สำหรับข้อมูลในระบบหรือไม่?', 'answer_type' => 'yes_no', 'risk_weight' => 0],
                ['question' => 'มีการกำหนด Access Control และ RBAC ที่เหมาะสมหรือไม่?', 'answer_type' => 'yes_no', 'risk_weight' => 0],
                ['question' => 'มีระบบ Audit Log สำหรับการเข้าถึงข้อมูลหรือไม่?', 'answer_type' => 'yes_no', 'risk_weight' => 0],
                ['question' => 'มีแผน Data Breach Response ที่ชัดเจนหรือไม่?', 'answer_type' => 'yes_no', 'risk_weight' => 0],
                ['question' => 'มาตรการเพิ่มเติมที่วางแผนจะดำเนินการ', 'answer_type' => 'text', 'risk_weight' => 0],
            ]],
        ];
    }

    private function liaTemplate(): array
    {
        return [
            ['title' => '1. การระบุประโยชน์อันชอบธรรม', 'questions' => [
                ['question' => 'ประโยชน์อันชอบธรรมที่ต้องการอ้างอิงคืออะไร?', 'answer_type' => 'text', 'risk_weight' => 0],
                ['question' => 'ประโยชน์นี้เป็นประโยชน์ของใคร? (บริษัท, บุคคลที่สาม, สังคม)', 'answer_type' => 'text', 'risk_weight' => 0],
                ['question' => 'ประโยชน์นี้มีความชัดเจน ถูกกฎหมาย และไม่ขัดต่อความสงบเรียบร้อยหรือไม่?', 'answer_type' => 'yes_no', 'risk_weight' => 8],
            ]],
            ['title' => '2. การทดสอบความจำเป็น (Necessity Test)', 'questions' => [
                ['question' => 'การประมวลผลนี้จำเป็นต่อการบรรลุประโยชน์หรือมีวิธีอื่นที่กระทบน้อยกว่า?', 'answer_type' => 'text', 'risk_weight' => 0],
                ['question' => 'ข้อมูลที่ใช้มีน้อยที่สุดเท่าที่จำเป็น (Data Minimization) หรือไม่?', 'answer_type' => 'yes_no', 'risk_weight' => 8],
                ['question' => 'มีการกำหนดระยะเวลาเก็บข้อมูลที่เหมาะสมหรือไม่?', 'answer_type' => 'yes_no', 'risk_weight' => 6],
            ]],
            ['title' => '3. การทดสอบความสมดุล (Balancing Test)', 'questions' => [
                ['question' => 'เจ้าของข้อมูลคาดหวังอะไรในสถานการณ์นี้?', 'answer_type' => 'text', 'risk_weight' => 0],
                ['question' => 'การประมวลผลอาจทำให้เกิดผลเสียต่อเจ้าของข้อมูลหรือไม่?', 'answer_type' => 'yes_no', 'risk_weight' => 12],
                ['question' => 'มีข้อมูลอ่อนไหว (มาตรา 26) เกี่ยวข้องหรือไม่?', 'answer_type' => 'yes_no', 'risk_weight' => 15],
                ['question' => 'ประโยชน์ขององค์กรมีน้ำหนักมากกว่าสิทธิและประโยชน์ของเจ้าของข้อมูลหรือไม่?', 'answer_type' => 'yes_no', 'risk_weight' => 10],
                ['question' => 'มีมาตรการป้องกัน (Safeguards) เช่น Opt-out, Transparency หรือไม่?', 'answer_type' => 'yes_no', 'risk_weight' => 0],
            ]],
            ['title' => '4. สรุปผลการประเมิน LIA', 'questions' => [
                ['question' => 'สามารถใช้ Legitimate Interest เป็นฐานทางกฎหมายได้หรือไม่?', 'answer_type' => 'yes_no', 'risk_weight' => 0],
                ['question' => 'มาตรการป้องกันเพิ่มเติมที่จำเป็น', 'answer_type' => 'text', 'risk_weight' => 0],
            ]],
        ];
    }

    private function gapTemplate(): array
    {
        return [
            ['title' => '1. นโยบายและกรอบการกำกับดูแล', 'questions' => [
                ['question' => 'มี Privacy Policy ที่ครบถ้วนและเป็นปัจจุบันหรือไม่?', 'answer_type' => 'yes_no', 'risk_weight' => 8],
                ['question' => 'มีการแต่งตั้ง DPO (Data Protection Officer) หรือไม่?', 'answer_type' => 'yes_no', 'risk_weight' => 10],
                ['question' => 'มีนโยบายภายในด้าน Data Protection ที่เป็นลายลักษณ์อักษรหรือไม่?', 'answer_type' => 'yes_no', 'risk_weight' => 8],
            ]],
            ['title' => '2. ROPA และการจัดทำเอกสาร', 'questions' => [
                ['question' => 'มีการจัดทำ ROPA ครบทุก Processing Activity หรือไม่?', 'answer_type' => 'yes_no', 'risk_weight' => 10],
                ['question' => 'ROPA ได้รับการ Review ในรอบ 12 เดือนที่ผ่านมาหรือไม่?', 'answer_type' => 'yes_no', 'risk_weight' => 6],
                ['question' => 'มีการระบุฐานทางกฎหมายทุก Processing Activity ใน ROPA หรือไม่?', 'answer_type' => 'yes_no', 'risk_weight' => 8],
            ]],
            ['title' => '3. Consent Management', 'questions' => [
                ['question' => 'มีระบบเก็บหลักฐานความยินยอม (Consent Record) หรือไม่?', 'answer_type' => 'yes_no', 'risk_weight' => 10],
                ['question' => 'ผู้ใช้สามารถถอนความยินยอมได้ง่ายหรือไม่?', 'answer_type' => 'yes_no', 'risk_weight' => 8],
                ['question' => 'ข้อความขอความยินยอมชัดเจน ไม่บังคับ และแยกจากเงื่อนไขอื่นหรือไม่?', 'answer_type' => 'yes_no', 'risk_weight' => 8],
            ]],
            ['title' => '4. สิทธิ์เจ้าของข้อมูล', 'questions' => [
                ['question' => 'มีกระบวนการรับและตอบสนองคำขอสิทธิ์ภายใน 30 วันหรือไม่?', 'answer_type' => 'yes_no', 'risk_weight' => 10],
                ['question' => 'มีช่องทางให้เจ้าของข้อมูลยื่นคำขอสิทธิ์หรือไม่?', 'answer_type' => 'yes_no', 'risk_weight' => 8],
            ]],
            ['title' => '5. Data Security และ Breach Management', 'questions' => [
                ['question' => 'มีมาตรการ Encryption สำหรับข้อมูล at-rest และ in-transit หรือไม่?', 'answer_type' => 'yes_no', 'risk_weight' => 8],
                ['question' => 'มีแผน Data Breach Response และ Incident Response หรือไม่?', 'answer_type' => 'yes_no', 'risk_weight' => 10],
                ['question' => 'มีระบบ Audit Log สำหรับการเข้าถึงข้อมูลสำคัญหรือไม่?', 'answer_type' => 'yes_no', 'risk_weight' => 8],
            ]],
            ['title' => '6. Vendor Management', 'questions' => [
                ['question' => 'มี Data Processing Agreement (DPA) กับ Vendor ทุกรายหรือไม่?', 'answer_type' => 'yes_no', 'risk_weight' => 10],
                ['question' => 'มีการ Review ความปลอดภัยของ Vendor เป็นประจำหรือไม่?', 'answer_type' => 'yes_no', 'risk_weight' => 6],
            ]],
            ['title' => '7. การฝึกอบรมและความตระหนัก', 'questions' => [
                ['question' => 'พนักงานได้รับการอบรม PDPA Awareness ภายใน 12 เดือนหรือไม่?', 'answer_type' => 'yes_no', 'risk_weight' => 8],
                ['question' => 'มีการทดสอบ Phishing Awareness สำหรับพนักงานหรือไม่?', 'answer_type' => 'yes_no', 'risk_weight' => 6],
            ]],
        ];
    }
}
