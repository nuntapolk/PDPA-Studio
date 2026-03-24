<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\BreachIncident;
use App\Models\BreachTimeline;
use App\Services\BreachService;
use Illuminate\Http\Request;

class BreachController extends Controller
{
    public function __construct(private BreachService $breachService) {}

    public function index(Request $request)
    {
        $orgId = $request->user()->organization_id;
        $incidents = BreachIncident::where('organization_id', $orgId)
            ->with(['reporter', 'assignee'])
            ->orderByRaw("FIELD(status, 'investigating', 'new', 'containing', 'notifying_pdpc', 'contained', 'resolved', 'closed')")
            ->orderBy('severity', 'desc')
            ->paginate(20);

        $stats = [
            'open' => BreachIncident::where('organization_id', $orgId)->whereNotIn('status', ['resolved', 'closed'])->count(),
            'critical' => BreachIncident::where('organization_id', $orgId)->where('severity', 'critical')->whereNotIn('status', ['resolved', 'closed'])->count(),
            'nearing_deadline' => BreachIncident::where('organization_id', $orgId)
                ->whereNull('pdpc_notified_at')
                ->where('requires_pdpc_notification', true)
                ->where('pdpc_notification_deadline', '<=', now()->addHours(24))
                ->where('pdpc_notification_deadline', '>', now())
                ->count(),
        ];

        return view('modules.breach.index', compact('incidents', 'stats'));
    }

    public function create()
    {
        return view('modules.breach.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'breach_type' => 'required|string',
            'severity' => 'required|in:low,medium,high,critical',
            'discovered_at' => 'required|date',
            'occurred_at' => 'nullable|date',
            'affected_count' => 'required|integer|min:0',
            'data_types_affected' => 'required|array',
            'includes_sensitive_data' => 'boolean',
            'requires_pdpc_notification' => 'boolean',
            'requires_subject_notification' => 'boolean',
        ]);

        $incident = $this->breachService->createIncident(
            $request->user()->organization_id,
            $request->user()->id,
            $validated
        );

        AuditLog::record('created', 'breach', $incident);
        return redirect()->route('breach.show', $incident)->with('success', 'รายงานเหตุการณ์สำเร็จ');
    }

    public function show(Request $request, BreachIncident $breach)
    {
        abort_unless($breach->organization_id === $request->user()->organization_id, 403);
        $breach->load(['reporter', 'assignee', 'timeline.user']);
        $hoursRemaining = $breach->hours_until_deadline;
        AuditLog::record('viewed', 'breach', $breach);
        return view('modules.breach.show', compact('breach', 'hoursRemaining'));
    }

    public function addTimeline(Request $request, BreachIncident $breach)
    {
        abort_unless($breach->organization_id === $request->user()->organization_id, 403);
        $request->validate(['action' => 'required|string', 'description' => 'nullable|string']);

        BreachTimeline::create([
            'breach_incident_id' => $breach->id,
            'user_id' => $request->user()->id,
            'action' => $request->action,
            'description' => $request->description,
        ]);

        return back()->with('success', 'บันทึก Timeline สำเร็จ');
    }

    public function notifyPdpc(Request $request, BreachIncident $breach)
    {
        abort_unless($breach->organization_id === $request->user()->organization_id, 403);
        $request->validate(['pdpc_reference_number' => 'nullable|string']);

        $breach->update([
            'pdpc_notified_at' => now(),
            'pdpc_reference_number' => $request->pdpc_reference_number,
            'status' => 'contained',
        ]);

        BreachTimeline::create([
            'breach_incident_id' => $breach->id,
            'user_id' => $request->user()->id,
            'action' => 'แจ้ง PDPC',
            'description' => 'แจ้งเหตุละเมิดต่อสำนักงานคณะกรรมการคุ้มครองข้อมูลส่วนบุคคล เลขอ้างอิง: ' . $request->pdpc_reference_number,
        ]);

        AuditLog::record('updated', 'breach', $breach);
        return back()->with('success', 'บันทึกการแจ้ง PDPC สำเร็จ');
    }
}
