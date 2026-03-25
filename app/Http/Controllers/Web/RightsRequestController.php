<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Organization;
use App\Models\RightsRequest;
use App\Models\RightsRequestNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RightsRequestController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $orgId = Auth::user()->organization_id;
        $perPage = in_array((int)$request->per_page, [50, 100, 200]) ? (int)$request->per_page : 50;

        $requests = RightsRequest::where('organization_id', $orgId)
            ->latest()
            ->paginate($perPage)->withQueryString();

        $pendingCount  = RightsRequest::where('organization_id', $orgId)->pending()->count();
        $overdueCount  = RightsRequest::where('organization_id', $orgId)->overdue()->count();
        $resolvedCount = RightsRequest::where('organization_id', $orgId)->where('status', 'completed')->count();

        return view('modules.rights.index', compact('requests', 'pendingCount', 'overdueCount', 'resolvedCount'));
    }

    public function show(RightsRequest $rightsRequest)
    {
        $this->authorizeOrg($rightsRequest->organization_id);

        $notes = $rightsRequest->notes()->orderBy('created_at')->get();

        AuditLog::record('viewed', 'rights', $rightsRequest);

        return view('modules.rights.show', compact('rightsRequest', 'notes'));
    }

    public function portal(string $slug)
    {
        $org = Organization::where('slug', $slug)->firstOrFail();
        return view('modules.rights.portal', compact('org'));
    }

    public function submitPublic(Request $request, string $slug)
    {
        $org = Organization::where('slug', $slug)->firstOrFail();

        $validated = $request->validate([
            'requester_name'  => 'required|string|max:255',
            'requester_email' => 'required|email|max:255',
            'requester_phone' => 'nullable|string|max:20',
            'request_type'    => 'required|string',
            'description'     => 'required|string',
        ]);

        $rightsRequest = RightsRequest::create([
            'organization_id' => $org->id,
            'requester_name'  => $validated['requester_name'],
            'requester_email' => $validated['requester_email'],
            'requester_phone' => $validated['requester_phone'] ?? null,
            'type'            => $validated['request_type'],
            'description'     => $validated['description'],
            'status'          => 'pending',
        ]);

        return back()->with('success', "ส่งคำขอเรียบร้อยแล้ว หมายเลขอ้างอิง: {$rightsRequest->ticket_number}");
    }

    public function updateStatus(Request $request, RightsRequest $rightsRequest)
    {
        $this->authorizeOrg($rightsRequest->organization_id);

        $validated = $request->validate([
            'status' => 'required|in:pending,in_review,awaiting_info,approved,completed,rejected,withdrawn',
        ]);

        $rightsRequest->update($validated);

        AuditLog::record('updated', 'rights', $rightsRequest);

        return back()->with('success', 'อัปเดตสถานะเรียบร้อยแล้ว');
    }

    public function addNote(Request $request, RightsRequest $rightsRequest)
    {
        $this->authorizeOrg($rightsRequest->organization_id);

        $validated = $request->validate([
            'note'       => 'required|string',
            'is_private' => 'nullable|boolean',
        ]);

        RightsRequestNote::create([
            'rights_request_id' => $rightsRequest->id,
            'user_id'           => Auth::id(),
            'note'              => $validated['note'],
            'is_internal'       => $request->boolean('is_private'),
        ]);

        return back()->with('success', 'เพิ่มบันทึกเรียบร้อยแล้ว');
    }

    private function authorizeOrg(int $orgId): void
    {
        if (Auth::user()->organization_id !== $orgId) {
            abort(403);
        }
    }
}
