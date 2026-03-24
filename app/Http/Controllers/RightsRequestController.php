<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\RightsRequest;
use App\Models\RightsRequestNote;
use Illuminate\Http\Request;

class RightsRequestController extends Controller
{
    public function index(Request $request)
    {
        $orgId = $request->user()->organization_id;
        $query = RightsRequest::where('organization_id', $orgId)->with(['assignee']);

        if ($request->status) $query->where('status', $request->status);
        if ($request->type) $query->where('type', $request->type);

        $requests = $query->orderByRaw("FIELD(status,'pending','in_review','awaiting_info','approved','completed','rejected','withdrawn')")
            ->orderBy('due_date')
            ->paginate(20);

        $stats = [
            'pending' => RightsRequest::where('organization_id', $orgId)->whereIn('status', ['pending', 'in_review', 'awaiting_info'])->count(),
            'overdue' => RightsRequest::where('organization_id', $orgId)->overdue()->count(),
            'completed_this_month' => RightsRequest::where('organization_id', $orgId)->where('status', 'completed')->whereMonth('completed_at', now()->month)->count(),
        ];

        return view('modules.rights.index', compact('requests', 'stats'));
    }

    public function show(Request $request, RightsRequest $rightsRequest)
    {
        abort_unless($rightsRequest->organization_id === $request->user()->organization_id, 403);
        $rightsRequest->load(['dataSubject', 'assignee', 'notes.user']);
        AuditLog::record('viewed', 'rights', $rightsRequest);
        return view('modules.rights.show', compact('rightsRequest'));
    }

    // Public Portal — รับคำร้องจากเจ้าของข้อมูล
    public function portal(string $slug)
    {
        $org = \App\Models\Organization::where('slug', $slug)->where('status', 'active')->firstOrFail();
        return view('modules.rights.portal', compact('org'));
    }

    public function submitPublic(Request $request, string $slug)
    {
        $org = \App\Models\Organization::where('slug', $slug)->firstOrFail();

        $validated = $request->validate([
            'type' => 'required|in:access,rectification,erasure,restriction,portability,objection',
            'requester_name' => 'required|string|max:255',
            'requester_email' => 'required|email',
            'requester_phone' => 'nullable|string|max:20',
            'requester_id_number' => 'nullable|string|max:13',
            'description' => 'required|string|max:2000',
        ]);

        RightsRequest::create(array_merge($validated, [
            'organization_id' => $org->id,
            'status' => 'pending',
            'submitted_at' => now(),
            'due_date' => now()->addDays(30),
        ]));

        // TODO: Send confirmation email to requester
        return redirect()->route('rights.portal', $slug)
            ->with('success', 'รับคำร้องของท่านเรียบร้อยแล้ว เราจะติดต่อกลับภายใน 30 วัน');
    }

    public function updateStatus(Request $request, RightsRequest $rightsRequest)
    {
        abort_unless($rightsRequest->organization_id === $request->user()->organization_id, 403);
        $request->validate([
            'status' => 'required|in:pending,in_review,awaiting_info,approved,completed,rejected,withdrawn',
            'response_note' => 'nullable|string',
            'rejection_reason' => 'nullable|string',
        ]);

        $old = $rightsRequest->status;
        $rightsRequest->update([
            'status' => $request->status,
            'response_note' => $request->response_note,
            'rejection_reason' => $request->rejection_reason,
            'completed_at' => $request->status === 'completed' ? now() : $rightsRequest->completed_at,
        ]);

        // Add internal note
        if ($request->response_note) {
            RightsRequestNote::create([
                'rights_request_id' => $rightsRequest->id,
                'user_id' => $request->user()->id,
                'note' => "[Status: {$old} → {$request->status}] " . $request->response_note,
                'is_internal' => true,
            ]);
        }

        AuditLog::record('updated', 'rights', $rightsRequest, ['status' => $old], ['status' => $request->status]);
        return back()->with('success', 'อัปเดตสถานะสำเร็จ');
    }

    public function addNote(Request $request, RightsRequest $rightsRequest)
    {
        abort_unless($rightsRequest->organization_id === $request->user()->organization_id, 403);
        $request->validate(['note' => 'required|string', 'is_internal' => 'boolean']);

        RightsRequestNote::create([
            'rights_request_id' => $rightsRequest->id,
            'user_id' => $request->user()->id,
            'note' => $request->note,
            'is_internal' => $request->boolean('is_internal', true),
        ]);

        return back()->with('success', 'เพิ่มบันทึกสำเร็จ');
    }
}
