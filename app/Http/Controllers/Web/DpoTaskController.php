<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\ComplianceChecklist;
use App\Models\DpoTask;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DpoTaskController extends Controller
{
    // ── Index ──────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        $orgId   = Auth::user()->organization_id;
        $perPage = in_array((int)$request->per_page, [50, 100, 200]) ? (int)$request->per_page : 50;

        $query = DpoTask::where('organization_id', $orgId)->with(['assignedTo', 'createdBy']);

        if ($request->filled('category')) $query->where('category', $request->category);
        if ($request->filled('priority')) $query->where('priority', $request->priority);
        if ($request->filled('status'))   $query->where('status', $request->status);
        if ($request->filled('search'))   $query->where('title', 'like', '%'.$request->search.'%');
        if ($request->filled('assigned')) $query->where('assigned_to', $request->assigned);

        // Default sort: overdue first, then by due_date
        $query->orderByRaw("CASE WHEN status NOT IN ('completed','cancelled') AND due_date < CURDATE() THEN 0 ELSE 1 END")
              ->orderByRaw("CASE WHEN due_date IS NULL THEN 1 ELSE 0 END")
              ->orderBy('due_date')
              ->orderByRaw("FIELD(priority,'urgent','high','medium','low')");

        $tasks = $query->paginate($perPage)->withQueryString();

        $base          = DpoTask::where('organization_id', $orgId);
        $totalCount    = (clone $base)->count();
        $overdueCount  = (clone $base)->overdue()->count();
        $inProgCount   = (clone $base)->inProgress()->count();
        $completedCount= (clone $base)->completed()->count();
        $urgentCount   = (clone $base)->urgent()->whereNotIn('status',['completed','cancelled'])->count();
        $dueThisWeek   = (clone $base)->dueThisWeek()->count();

        // Compliance score
        $clTotal = ComplianceChecklist::where('organization_id', $orgId)->where('status', '!=', 'na')->count();
        $clDone  = ComplianceChecklist::where('organization_id', $orgId)->where('status', 'completed')->count();
        $complianceScore = $clTotal > 0 ? round($clDone / $clTotal * 100) : 0;

        $members = User::where('organization_id', $orgId)->get();

        return view('modules.dpo.index', compact(
            'tasks', 'totalCount', 'overdueCount', 'inProgCount',
            'completedCount', 'urgentCount', 'dueThisWeek',
            'complianceScore', 'clDone', 'clTotal', 'members'
        ));
    }

    // ── Create ─────────────────────────────────────────────────────────────
    public function create()
    {
        $members = User::where('organization_id', Auth::user()->organization_id)->get();
        return view('modules.dpo.create', compact('members'));
    }

    // ── Store ──────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $orgId = Auth::user()->organization_id;

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'category'    => 'required|in:compliance_review,policy_update,training,audit,vendor_review,incident_response,reporting,other',
            'priority'    => 'required|in:low,medium,high,urgent',
            'due_date'    => 'nullable|date',
            'assigned_to' => 'nullable|exists:users,id',
            'notes'       => 'nullable|string',
        ]);

        $task = DpoTask::create([
            ...$validated,
            'organization_id' => $orgId,
            'status'          => 'pending',
            'created_by'      => Auth::id(),
        ]);

        AuditLog::record('created', 'dpo_task', $task);

        return redirect()->route('dpo.show', $task)
                         ->with('success', 'สร้างงานใหม่สำเร็จ');
    }

    // ── Show ───────────────────────────────────────────────────────────────
    public function show(DpoTask $dpo)
    {
        $this->authorizeOrg($dpo->organization_id);
        $dpo->load(['assignedTo', 'createdBy']);
        AuditLog::record('viewed', 'dpo_task', $dpo);
        return view('modules.dpo.show', compact('dpo'));
    }

    // ── Edit ───────────────────────────────────────────────────────────────
    public function edit(DpoTask $dpo)
    {
        $this->authorizeOrg($dpo->organization_id);
        $members = User::where('organization_id', Auth::user()->organization_id)->get();
        return view('modules.dpo.edit', compact('dpo', 'members'));
    }

    // ── Update ─────────────────────────────────────────────────────────────
    public function update(Request $request, DpoTask $dpo)
    {
        $this->authorizeOrg($dpo->organization_id);

        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'category'    => 'required|in:compliance_review,policy_update,training,audit,vendor_review,incident_response,reporting,other',
            'priority'    => 'required|in:low,medium,high,urgent',
            'status'      => 'required|in:pending,in_progress,completed,cancelled',
            'due_date'    => 'nullable|date',
            'assigned_to' => 'nullable|exists:users,id',
            'notes'       => 'nullable|string',
        ]);

        if ($validated['status'] === 'completed' && $dpo->status !== 'completed') {
            $validated['completed_at'] = now();
        }

        $dpo->update($validated);
        AuditLog::record('updated', 'dpo_task', $dpo);

        return redirect()->route('dpo.show', $dpo)
                         ->with('success', 'บันทึกการแก้ไขสำเร็จ');
    }

    // ── Quick Status Update ────────────────────────────────────────────────
    public function updateStatus(Request $request, DpoTask $dpo)
    {
        $this->authorizeOrg($dpo->organization_id);
        $request->validate(['status' => 'required|in:pending,in_progress,completed,cancelled']);

        $data = ['status' => $request->status];
        if ($request->status === 'completed') $data['completed_at'] = now();

        $dpo->update($data);
        AuditLog::record('status_updated', 'dpo_task', $dpo);

        return back()->with('success', 'อัปเดตสถานะเป็น "'.DpoTask::statusLabel($request->status).'" สำเร็จ');
    }

    // ── Destroy ────────────────────────────────────────────────────────────
    public function destroy(DpoTask $dpo)
    {
        $this->authorizeOrg($dpo->organization_id);
        AuditLog::record('deleted', 'dpo_task', $dpo);
        $dpo->delete();
        return redirect()->route('dpo.index')->with('success', 'ลบงานสำเร็จ');
    }

    // ── Compliance Checklist ───────────────────────────────────────────────
    public function checklist(Request $request)
    {
        $orgId = Auth::user()->organization_id;
        $filterCat = $request->get('category');

        $query = ComplianceChecklist::where('organization_id', $orgId);
        if ($filterCat) $query->where('category', $filterCat);
        $items = $query->orderBy('category')->orderBy('sort_order')->get()->groupBy('category');

        $categories = ['consent','rights','ropa','breach','security','policy','training','vendor'];

        // Per-category scores
        $scores = [];
        foreach ($categories as $cat) {
            $total = ComplianceChecklist::where('organization_id', $orgId)
                        ->where('category', $cat)->where('status', '!=', 'na')->count();
            $done  = ComplianceChecklist::where('organization_id', $orgId)
                        ->where('category', $cat)->where('status', 'completed')->count();
            $scores[$cat] = $total > 0 ? round($done / $total * 100) : 0;
        }

        $overallTotal = ComplianceChecklist::where('organization_id', $orgId)->where('status', '!=', 'na')->count();
        $overallDone  = ComplianceChecklist::where('organization_id', $orgId)->where('status', 'completed')->count();
        $overallScore = $overallTotal > 0 ? round($overallDone / $overallTotal * 100) : 0;

        $members = User::where('organization_id', $orgId)->get();

        return view('modules.dpo.checklist', compact(
            'items', 'categories', 'scores', 'overallScore',
            'overallDone', 'overallTotal', 'filterCat', 'members'
        ));
    }

    // ── Helpers ────────────────────────────────────────────────────────────
    private function authorizeOrg(int $orgId): void
    {
        if (Auth::user()->organization_id !== $orgId) {
            abort(403);
        }
    }

    // ── Update Checklist Item ──────────────────────────────────────────────
    public function updateChecklistItem(Request $request, ComplianceChecklist $item)
    {
        $this->authorizeOrg($item->organization_id);

        $request->validate([
            'status'           => 'required|in:not_started,in_progress,completed,na',
            'notes'            => 'nullable|string',
            'responsible_user' => 'nullable|exists:users,id',
            'due_date'         => 'nullable|date',
        ]);

        $data = $request->only(['status', 'notes', 'responsible_user', 'due_date']);
        if ($request->status === 'completed' && !$item->completed_at) {
            $data['completed_at'] = now()->toDateString();
        }
        if ($request->status !== 'completed') {
            $data['completed_at'] = null;
        }

        $item->update($data);
        return back()->with('success', 'อัปเดต Checklist สำเร็จ');
    }
}
