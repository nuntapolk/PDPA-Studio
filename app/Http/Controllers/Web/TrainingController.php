<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\TrainingCompletion;
use App\Models\TrainingCourse;
use App\Models\TrainingQuestion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrainingController extends Controller
{
    // ── Index: course catalogue ────────────────────────────────────────────
    public function index(Request $request)
    {
        $orgId  = Auth::user()->organization_id;
        $userId = Auth::id();

        $query = TrainingCourse::where('organization_id', $orgId)
            ->withCount(['completions as passed_count' => fn($q) => $q->where('passed', true)])
            ->with(['completions' => fn($q) => $q->where('user_id', $userId)->orderByDesc('id')->limit(1)]);

        if ($request->filled('search'))   $query->where('title', 'like', '%'.$request->search.'%');
        if ($request->boolean('required')) $query->where('is_required', true);

        $courses = $query->latest()->get();

        // Stats
        $orgUserCount  = User::where('organization_id', $orgId)->count();
        $totalCourses  = TrainingCourse::where('organization_id', $orgId)->where('is_active', true)->count();
        $myCompleted   = TrainingCompletion::whereIn('course_id',
                            TrainingCourse::where('organization_id', $orgId)->pluck('id'))
                        ->where('user_id', $userId)->where('passed', true)->distinct('course_id')->count();
        $certCount     = TrainingCompletion::whereIn('course_id',
                            TrainingCourse::where('organization_id', $orgId)->pluck('id'))
                        ->whereNotNull('certificate_number')->where('passed', true)->count();
        $expiringSoon  = TrainingCompletion::whereIn('course_id',
                            TrainingCourse::where('organization_id', $orgId)->pluck('id'))
                        ->where('passed', true)->whereNotNull('expires_at')
                        ->where('expires_at', '>', now())->where('expires_at', '<=', now()->addDays(30))->count();

        return view('modules.training.index', compact(
            'courses', 'orgUserCount', 'totalCourses', 'myCompleted', 'certCount', 'expiringSoon'
        ));
    }

    // ── Show: course detail + quiz ─────────────────────────────────────────
    public function show(TrainingCourse $course, Request $request)
    {
        $this->authorizeOrg($course->organization_id);
        $userId = Auth::id();

        $questions = $course->questions()->orderBy('sort_order')->get();
        $myLatest  = TrainingCompletion::where('course_id', $course->id)
                        ->where('user_id', $userId)->orderByDesc('id')->first();
        $attempts  = TrainingCompletion::where('course_id', $course->id)
                        ->where('user_id', $userId)->orderByDesc('id')->get();

        $allCompletions = TrainingCompletion::where('course_id', $course->id)
            ->where('passed', true)->with('user')->get();

        AuditLog::record('viewed', 'training_course', $course);

        return view('modules.training.show', compact(
            'course', 'questions', 'myLatest', 'attempts', 'allCompletions'
        ));
    }

    // ── Submit Quiz ────────────────────────────────────────────────────────
    public function submitQuiz(Request $request, TrainingCourse $course)
    {
        $this->authorizeOrg($course->organization_id);
        $userId   = Auth::id();
        $questions = $course->questions()->orderBy('sort_order')->get();

        if ($questions->isEmpty()) {
            return back()->with('error', 'ยังไม่มีคำถามในคอร์สนี้');
        }

        // Calculate score
        $correct = 0;
        foreach ($questions as $q) {
            $submitted = $request->input('answer_'.$q->id);
            if ($submitted && $q->isCorrect($submitted)) $correct++;
        }
        $score  = (int)round($correct / $questions->count() * 100);
        $passed = $score >= $course->passing_score;

        $attempt = TrainingCompletion::where('course_id', $course->id)
                    ->where('user_id', $userId)->count() + 1;

        $certNumber = null;
        $expiresAt  = null;
        if ($passed && $course->certificate_enabled) {
            $certNumber = TrainingCompletion::generateCertNumber($course->id, $userId);
            $expiresAt  = $course->validity_months
                ? now()->addMonths($course->validity_months) : null;
        }

        $completion = TrainingCompletion::create([
            'course_id'          => $course->id,
            'user_id'            => $userId,
            'score'              => $score,
            'passed'             => $passed,
            'attempt_number'     => $attempt,
            'certificate_number' => $certNumber,
            'started_at'         => now()->subMinutes(rand(5,30)),
            'completed_at'       => now(),
            'expires_at'         => $expiresAt,
        ]);

        AuditLog::record('completed', 'training_course', $course);

        return redirect()->route('training.result', [$course, $completion]);
    }

    // ── Result ─────────────────────────────────────────────────────────────
    public function result(TrainingCourse $course, TrainingCompletion $completion)
    {
        $this->authorizeOrg($course->organization_id);
        abort_if($completion->user_id !== Auth::id(), 403);
        $questions = $course->questions()->orderBy('sort_order')->get();
        return view('modules.training.result', compact('course', 'completion', 'questions'));
    }

    // ── Create / Edit / Store / Update ─────────────────────────────────────
    public function create()
    {
        return view('modules.training.create');
    }

    public function store(Request $request)
    {
        $orgId = Auth::user()->organization_id;
        $validated = $request->validate([
            'title'               => 'required|string|max:255',
            'description'         => 'nullable|string',
            'content'             => 'nullable|string',
            'duration_minutes'    => 'required|integer|min:1',
            'is_required'         => 'boolean',
            'passing_score'       => 'required|integer|min:1|max:100',
            'certificate_enabled' => 'boolean',
            'validity_months'     => 'required|integer|min:1',
            'questions'           => 'nullable|array',
            'questions.*.question'       => 'required_with:questions|string',
            'questions.*.options'        => 'required_with:questions|array|min:2',
            'questions.*.correct_answer' => 'required_with:questions|string',
        ]);

        $course = TrainingCourse::create([
            'organization_id'     => $orgId,
            'title'               => $validated['title'],
            'description'         => $validated['description'] ?? null,
            'content'             => $validated['content'] ?? null,
            'duration_minutes'    => $validated['duration_minutes'],
            'is_required'         => $request->boolean('is_required'),
            'passing_score'       => $validated['passing_score'],
            'certificate_enabled' => $request->boolean('certificate_enabled'),
            'validity_months'     => $validated['validity_months'],
            'is_active'           => true,
            'created_by'          => Auth::id(),
        ]);

        foreach (($request->questions ?? []) as $i => $q) {
            if (empty($q['question'])) continue;
            TrainingQuestion::create([
                'course_id'      => $course->id,
                'question'       => $q['question'],
                'options'        => $q['options'],
                'correct_answer' => $q['correct_answer'],
                'explanation'    => $q['explanation'] ?? null,
                'sort_order'     => $i + 1,
            ]);
        }

        return redirect()->route('training.show', $course)->with('success', 'สร้างคอร์สสำเร็จ');
    }

    public function edit(TrainingCourse $course)
    {
        $this->authorizeOrg($course->organization_id);
        $questions = $course->questions()->orderBy('sort_order')->get();
        return view('modules.training.edit', compact('course', 'questions'));
    }

    public function update(Request $request, TrainingCourse $course)
    {
        $this->authorizeOrg($course->organization_id);
        $validated = $request->validate([
            'title'               => 'required|string|max:255',
            'description'         => 'nullable|string',
            'content'             => 'nullable|string',
            'duration_minutes'    => 'required|integer|min:1',
            'passing_score'       => 'required|integer|min:1|max:100',
            'validity_months'     => 'required|integer|min:1',
        ]);

        $course->update([
            ...$validated,
            'is_required'         => $request->boolean('is_required'),
            'certificate_enabled' => $request->boolean('certificate_enabled'),
        ]);

        // Rebuild questions
        $course->questions()->delete();
        foreach (($request->questions ?? []) as $i => $q) {
            if (empty($q['question'])) continue;
            TrainingQuestion::create([
                'course_id'      => $course->id,
                'question'       => $q['question'],
                'options'        => $q['options'],
                'correct_answer' => $q['correct_answer'],
                'explanation'    => $q['explanation'] ?? null,
                'sort_order'     => $i + 1,
            ]);
        }

        return redirect()->route('training.show', $course)->with('success', 'บันทึกการแก้ไขสำเร็จ');
    }

    // ── Report ─────────────────────────────────────────────────────────────
    public function report(Request $request)
    {
        $orgId   = Auth::user()->organization_id;
        $perPage = in_array((int)$request->per_page, [50,100,200]) ? (int)$request->per_page : 50;

        $courses = TrainingCourse::where('organization_id', $orgId)->where('is_active', true)->get();
        $users   = User::where('organization_id', $orgId)
                    ->with(['completions' => fn($q) => $q->where('passed', true)
                        ->whereIn('course_id', $courses->pluck('id'))])
                    ->paginate($perPage)->withQueryString();

        $totalUsers    = User::where('organization_id', $orgId)->count();
        $fullCompleted = 0; // users who passed all required courses
        $requiredIds   = TrainingCourse::where('organization_id', $orgId)
                            ->where('is_required', true)->pluck('id');

        if ($requiredIds->isNotEmpty()) {
            $fullCompleted = User::where('organization_id', $orgId)
                ->whereDoesntHave('completions', fn($q) =>
                    $q->where('passed', false)->orWhereNotIn('course_id', $requiredIds))
                ->whereHas('completions', fn($q) =>
                    $q->where('passed', true)->whereIn('course_id', $requiredIds))
                ->count();
        }

        return view('modules.training.report', compact(
            'courses', 'users', 'totalUsers', 'fullCompleted', 'requiredIds', 'perPage'
        ));
    }

    // ── Toggle active ──────────────────────────────────────────────────────
    public function toggleActive(TrainingCourse $course)
    {
        $this->authorizeOrg($course->organization_id);
        $course->update(['is_active' => !$course->is_active]);
        return back()->with('success', $course->is_active ? 'เปิดใช้งานคอร์สแล้ว' : 'ปิดคอร์สแล้ว');
    }

    // ── Destroy ────────────────────────────────────────────────────────────
    public function destroy(TrainingCourse $course)
    {
        $this->authorizeOrg($course->organization_id);
        $course->delete();
        return redirect()->route('training.index')->with('success', 'ลบคอร์สสำเร็จ');
    }
}
