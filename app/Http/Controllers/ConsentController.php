<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Consent;
use App\Models\ConsentTemplate;
use App\Models\DataSubject;
use Illuminate\Http\Request;

class ConsentController extends Controller
{
    public function index(Request $request)
    {
        $orgId = $request->user()->organization_id;
        $templates = ConsentTemplate::where('organization_id', $orgId)
            ->withCount(['consents as granted_count' => fn($q) => $q->where('granted', true)])
            ->withCount(['consents as withdrawn_count' => fn($q) => $q->whereNotNull('withdrawn_at')])
            ->latest()->paginate(20);

        return view('modules.consent.index', compact('templates'));
    }

    public function show(Request $request, ConsentTemplate $template)
    {
        $this->authorizeOrg($request, $template);
        $consents = $template->consents()->with('dataSubject')->latest()->paginate(30);
        AuditLog::record('viewed', 'consent', $template);
        return view('modules.consent.show', compact('template', 'consents'));
    }

    public function create()
    {
        return view('modules.consent.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'purpose' => 'required|string',
            'legal_basis' => 'required|in:consent,contract,legal_obligation,legitimate_interest,public_interest,vital_interest',
            'retention_days' => 'required|integer|min:1',
            'data_categories' => 'required|string',
            'is_sensitive' => 'boolean',
            'requires_explicit_consent' => 'boolean',
            'withdrawal_info' => 'nullable|string',
        ]);

        $template = ConsentTemplate::create(array_merge($validated, [
            'organization_id' => $request->user()->organization_id,
            'slug' => \Illuminate\Support\Str::slug($validated['name']) . '-' . $request->user()->organization_id,
            'created_by' => $request->user()->id,
        ]));

        AuditLog::record('created', 'consent', $template);
        return redirect()->route('consent.show', $template)->with('success', 'สร้าง Consent Template สำเร็จ');
    }

    public function withdraw(Request $request, Consent $consent)
    {
        $this->authorizeOrg($request, $consent);
        $request->validate(['withdrawal_reason' => 'nullable|string']);

        $consent->update([
            'withdrawn_at' => now(),
            'withdrawal_reason' => $request->withdrawal_reason,
        ]);

        AuditLog::record('updated', 'consent', $consent, ['withdrawn_at' => null], ['withdrawn_at' => now()]);
        return back()->with('success', 'ถอนความยินยอมสำเร็จ');
    }

    public function widgetCode(Request $request, ConsentTemplate $template)
    {
        $this->authorizeOrg($request, $template);
        $apiKey = $request->user()->organization->apiKeys()->first();
        return view('modules.consent.widget', compact('template', 'apiKey'));
    }

    private function authorizeOrg(Request $request, $model): void
    {
        abort_unless($model->organization_id === $request->user()->organization_id, 403);
    }
}
