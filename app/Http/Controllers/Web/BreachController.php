<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\BreachIncident;
use App\Models\BreachTimeline;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BreachController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $orgId = Auth::user()->organization_id;
        $perPage = in_array((int)$request->per_page, [50, 100, 200]) ? (int)$request->per_page : 50;

        $breaches = BreachIncident::where('organization_id', $orgId)
            ->latest()
            ->paginate($perPage)->withQueryString();

        $openCount     = BreachIncident::where('organization_id', $orgId)->whereNotIn('status', ['resolved', 'closed'])->count();
        $criticalCount = BreachIncident::where('organization_id', $orgId)->where('severity', 'critical')->whereNotIn('status', ['resolved', 'closed'])->count();
        $resolvedCount = BreachIncident::where('organization_id', $orgId)->whereIn('status', ['resolved', 'closed'])->count();

        return view('modules.breach.index', compact('breaches', 'openCount', 'criticalCount', 'resolvedCount'));
    }

    public function create()
    {
        return view('modules.breach.create');
    }

    public function store(Request $request)
    {
        $orgId = Auth::user()->organization_id;

        $validated = $request->validate([
            'title'              => 'required|string|max:255',
            'description'        => 'required|string',
            'breach_type'        => 'required|string',
            'severity'           => 'required|in:low,medium,high,critical',
            'discovered_at'      => 'required|date',
            'affected_count'     => 'nullable|integer|min:0',
            'data_types_affected'=> 'nullable|array',
        ]);

        $breach = BreachIncident::create([
            'organization_id'     => $orgId,
            'title'               => $validated['title'],
            'description'         => $validated['description'],
            'breach_type'         => $validated['breach_type'],
            'severity'            => $validated['severity'],
            'discovered_at'       => $validated['discovered_at'],
            'affected_count'      => $validated['affected_count'] ?? 0,
            'data_types_affected' => $request->input('data_types_affected', []),
            'status'              => 'new',
            'reported_by'         => Auth::id(),
            'requires_pdpc_notification' => in_array($validated['severity'], ['high', 'critical']),
        ]);

        AuditLog::record('created', 'breach', $breach);

        return redirect()->route('breach.show', $breach)->with('success', "รายงาน Data Breach เรียบร้อย — {$breach->incident_number}");
    }

    public function show(BreachIncident $breach)
    {
        $this->authorizeOrg($breach->organization_id);

        $timelines = $breach->timeline()->orderBy('created_at')->get();

        AuditLog::record('viewed', 'breach', $breach);

        return view('modules.breach.show', compact('breach', 'timelines'));
    }

    public function addTimeline(Request $request, BreachIncident $breach)
    {
        $this->authorizeOrg($breach->organization_id);

        $validated = $request->validate([
            'event'       => 'required|string',
            'description' => 'nullable|string',
        ]);

        BreachTimeline::create([
            'breach_incident_id' => $breach->id,
            'action'             => $validated['event'],
            'description'        => $validated['description'] ?? null,
            'user_id'            => Auth::id(),
        ]);

        return back()->with('success', 'เพิ่ม Timeline เรียบร้อยแล้ว');
    }

    public function notifyPdpc(Request $request, BreachIncident $breach)
    {
        $this->authorizeOrg($breach->organization_id);

        $breach->update([
            'pdpc_notified_at' => now(),
            'status'           => 'notifying_pdpc',
        ]);

        BreachTimeline::create([
            'breach_incident_id' => $breach->id,
            'action'             => 'pdpc_notified',
            'description'        => 'แจ้ง PDPC แล้ว',
            'user_id'            => Auth::id(),
        ]);

        AuditLog::record('notified_pdpc', 'breach', $breach);

        return back()->with('success', 'บันทึกการแจ้ง PDPC เรียบร้อยแล้ว');
    }

    private function authorizeOrg(int $orgId): void
    {
        if (Auth::user()->organization_id !== $orgId) {
            abort(403);
        }
    }
}
