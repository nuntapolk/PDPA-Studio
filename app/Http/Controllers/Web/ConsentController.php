<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Consent;
use App\Models\ConsentTemplate;
use App\Models\DataSubject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConsentController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $orgId = Auth::user()->organization_id;
        $perPage = in_array((int)$request->per_page, [50, 100, 200]) ? (int)$request->per_page : 50;

        $templates = ConsentTemplate::where('organization_id', $orgId)
            ->withCount(['consents' => fn($q) => $q->where('granted', true)->whereNull('withdrawn_at')])
            ->latest()
            ->paginate($perPage)->withQueryString();

        $totalActive    = Consent::where('organization_id', $orgId)->active()->count();
        $totalWithdrawn = Consent::where('organization_id', $orgId)->whereNotNull('withdrawn_at')->count();
        $expiringSoon  = Consent::where('organization_id', $orgId)->expiringSoon()->count();

        return view('modules.consent.index', compact('templates', 'totalActive', 'totalWithdrawn', 'expiringSoon'));
    }

    public function show(ConsentTemplate $template)
    {
        $this->authorizeOrg($template->organization_id);

        $consents = Consent::where('template_id', $template->id)
            ->with('dataSubject')
            ->latest()
            ->paginate(25);

        AuditLog::record('viewed', 'consent', $template);

        return view('modules.consent.show', compact('template', 'consents'));
    }

    public function create()
    {
        return view('modules.consent.create');
    }

    public function store(Request $request)
    {
        $orgId = Auth::user()->organization_id;

        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'purpose'     => 'required|string',
            'legal_basis' => 'required|string',
            'category'    => 'required|string',
            'is_required' => 'nullable|boolean',
            'validity_days' => 'nullable|integer|min:1',
        ]);

        $template = ConsentTemplate::create(array_merge($validated, [
            'organization_id' => $orgId,
            'version'  => '1.0',
            'is_active' => true,
            'created_by' => Auth::id(),
        ]));

        AuditLog::record('created', 'consent', $template);

        return redirect()->route('consent.show', $template)->with('success', 'สร้าง Consent Template สำเร็จ');
    }

    public function withdraw(Request $request, Consent $consent)
    {
        $this->authorizeOrg($consent->organization_id);

        $consent->update([
            'granted'          => false,
            'withdrawn_at'     => now(),
            'withdrawal_reason' => 'user_request',
        ]);

        AuditLog::record('withdrawn', 'consent', $consent);

        return back()->with('success', 'ถอนความยินยอมเรียบร้อยแล้ว');
    }

    public function widgetCode(ConsentTemplate $template)
    {
        $this->authorizeOrg($template->organization_id);
        return view('modules.consent.widget', compact('template'));
    }

    private function authorizeOrg(int $orgId): void
    {
        if (Auth::user()->organization_id !== $orgId) {
            abort(403);
        }
    }
}
