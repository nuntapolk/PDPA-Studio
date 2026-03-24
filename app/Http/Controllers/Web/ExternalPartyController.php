<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\DataProcessingAgreement;
use App\Models\ExternalParty;
use App\Models\ExternalPartyAssessment;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExternalPartyController extends Controller
{
    public function index(Request $request)
    {
        $perPage = in_array((int)$request->per_page,[50,100,200]) ? (int)$request->per_page : 50;
        $query = ExternalParty::with(['activeDpa','latestAssessment']);

        if ($request->filled('type'))    $query->where('relationship_type', $request->type);
        if ($request->filled('status'))  $query->where('status', $request->status);
        if ($request->filled('risk'))    $query->where('risk_level', $request->risk);
        if ($request->boolean('cross'))  $query->where('is_cross_border', true);
        if ($request->boolean('no_dpa')) $query->noDpa();
        if ($request->filled('search'))  $query->where(function($q) use ($request) {
            $q->where('name','like','%'.$request->search.'%')
              ->orWhere('code','like','%'.$request->search.'%');
        });

        $parties = $query->latest()->paginate($perPage)->withQueryString();

        $stats = [
            'total'         => ExternalParty::count(),
            'processors'    => ExternalParty::where('relationship_type','data_processor')->count(),
            'controllers'   => ExternalParty::where('relationship_type','data_controller')->count(),
            'joint'         => ExternalParty::where('relationship_type','joint_controller')->count(),
            'cross_border'  => ExternalParty::where('is_cross_border',true)->count(),
            'no_dpa'        => ExternalParty::active()->noDpa()
                                ->whereIn('relationship_type',['data_processor','data_controller','joint_controller'])->count(),
            'dpa_expiring'  => DataProcessingAgreement::where('status','active')
                                ->whereNotNull('expires_at')
                                ->where('expires_at','<=',now()->addDays(60))
                                ->where('expires_at','>=',now())->count(),
            'review_overdue'=> ExternalParty::active()->where('next_review_date','<',now())->count(),
        ];

        return view('modules.parties.index', compact('parties','stats','perPage'));
    }

    public function create()
    {
        return view('modules.parties.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'              => 'required|string|max:255',
            'relationship_type' => 'required|string',
            'type'              => 'required|string',
            'country'           => 'required|string|max:2',
            'risk_level'        => 'required|string',
            'status'            => 'required|string',
        ]);

        // Handle transfer_countries_raw → json array
        $transferCountries = null;
        if ($request->filled('transfer_countries_raw')) {
            $transferCountries = array_filter(array_map('trim', explode(',', $request->transfer_countries_raw)));
        }

        $party = ExternalParty::create(array_merge(
            $request->except(['_token','dpa_title','dpa_type','dpa_status','dpa_signed_at','dpa_expires_at','dpa_our_role','dpa_their_role','transfer_countries_raw']),
            [
                'created_by'          => Auth::id(),
                'data_types_shared'   => $request->data_types_shared ? array_filter((array)$request->data_types_shared) : null,
                'processing_purposes' => $request->processing_purposes ? array_filter((array)$request->processing_purposes) : null,
                'transfer_countries'  => $transferCountries ?: null,
                'is_cross_border'     => $request->boolean('is_cross_border'),
                'tia_required'        => $request->boolean('tia_required'),
            ]
        ));

        // Create DPA if provided
        if ($request->filled('dpa_title')) {
            DataProcessingAgreement::create([
                'external_party_id' => $party->id,
                'title'             => $request->dpa_title,
                'type'              => $request->dpa_type ?? 'dpa',
                'our_role'          => $request->dpa_our_role ?? 'controller',
                'their_role'        => $request->dpa_their_role ?? 'processor',
                'status'            => $request->dpa_status ?? 'draft',
                'signed_at'         => $request->dpa_signed_at ?: null,
                'expires_at'        => $request->dpa_expires_at ?: null,
                'effective_at'      => $request->dpa_signed_at ?: null,
                'breach_notification_hours' => 72,
                'created_by'        => Auth::id(),
            ]);
        }

        AuditLog::record('created', 'external_party', $party);
        return redirect()->route('parties.show', $party)->with('success', 'เพิ่ม External Party สำเร็จ');
    }

    public function show(ExternalParty $party)
    {
        $party->load(['dpas.creator','assessments.assessor','ropaRecords','creator']);
        AuditLog::record('viewed', 'external_party', $party);
        return view('modules.parties.show', compact('party'));
    }

    public function edit(ExternalParty $party)
    {
        return view('modules.parties.edit', compact('party'));
    }

    public function update(Request $request, ExternalParty $party)
    {
        $before = $party->only(['name','relationship_type','status','risk_level']);

        // Handle textarea-based array inputs from edit form
        $dataTypes = null;
        if ($request->filled('data_types_raw')) {
            $dataTypes = array_filter(array_map('trim', explode("\n", $request->data_types_raw)));
        } elseif ($request->data_types_shared) {
            $dataTypes = array_filter((array)$request->data_types_shared);
        }

        $purposes = null;
        if ($request->filled('purposes_raw')) {
            $purposes = array_filter(array_map('trim', explode("\n", $request->purposes_raw)));
        } elseif ($request->processing_purposes) {
            $purposes = array_filter((array)$request->processing_purposes);
        }

        // Handle transfer_countries_raw → json
        $transferCountries = null;
        if ($request->filled('transfer_countries_raw')) {
            $transferCountries = array_filter(array_map('trim', explode(',', $request->transfer_countries_raw)));
        }

        $party->update(array_merge(
            $request->except(['_token','_method','data_types_raw','purposes_raw','transfer_countries_raw']),
            [
                'data_types_shared'   => $dataTypes ?: null,
                'processing_purposes' => $purposes ?: null,
                'transfer_countries'  => $transferCountries ?: $party->transfer_countries,
                'is_cross_border'     => $request->boolean('is_cross_border'),
                'tia_required'        => $request->boolean('tia_required'),
            ]
        ));
        AuditLog::record('updated', 'external_party', $party, $before, $party->only(array_keys($before)));
        return redirect()->route('parties.show', $party)->with('success', 'บันทึกการแก้ไขสำเร็จ');
    }

    public function destroy(ExternalParty $party)
    {
        AuditLog::record('deleted', 'external_party', $party);
        $party->delete();
        return redirect()->route('parties.index')->with('success', 'ลบสำเร็จ');
    }

    // ── DPA Management ────────────────────────────────────────────────────────
    public function storeDpa(Request $request, ExternalParty $party)
    {
        $request->validate(['title'=>'required','type'=>'required','our_role'=>'required','their_role'=>'required']);
        DataProcessingAgreement::create(array_merge($request->except(['_token']), [
            'external_party_id' => $party->id,
            'created_by'        => Auth::id(),
            'data_categories'   => $party->data_types_shared ?? [],
        ]));
        AuditLog::record('created', 'dpa', $party);
        return back()->with('success', 'เพิ่ม DPA สำเร็จ');
    }

    public function updateDpa(Request $request, ExternalParty $party, DataProcessingAgreement $dpa)
    {
        $dpa->update($request->except(['_token','_method']));
        return back()->with('success', 'บันทึก DPA สำเร็จ');
    }

    // ── Assessment ────────────────────────────────────────────────────────────
    public function storeAssessment(Request $request, ExternalParty $party)
    {
        $request->validate(['score'=>'required|integer|min:0|max:100','risk_level'=>'required']);
        ExternalPartyAssessment::create(array_merge($request->except(['_token']), [
            'external_party_id' => $party->id,
            'assessed_by'       => Auth::id(),
        ]));
        $party->update(['risk_level'=>$request->risk_level, 'next_review_date'=>$request->next_assessment_date]);
        AuditLog::record('assessed', 'external_party', $party);
        return back()->with('success', 'บันทึกผลการประเมินสำเร็จ');
    }
}
