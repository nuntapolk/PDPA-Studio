<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\DataProcessingAgreement;
use App\Models\ExternalParty;
use App\Models\Organization;
use App\Models\RopaRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DataMapController extends Controller
{
    public function index(Request $request)
    {
        $org = Organization::find(Auth::user()->organization_id);

        $parties = ExternalParty::with(['activeDpa','latestAssessment'])
            ->where('status','!=','terminated')
            ->orderBy('relationship_type')
            ->orderBy('name')
            ->get();

        // Group by relationship type
        $grouped = $parties->groupBy('relationship_type');

        // Stats for map
        $stats = [
            'total_parties'   => $parties->count(),
            'with_active_dpa' => $parties->filter(fn($p) => $p->dpa_status === 'active')->count(),
            'cross_border'    => $parties->where('is_cross_border', true)->count(),
            'high_risk'       => $parties->whereIn('risk_level', ['high','critical'])->count(),
            'no_dpa'          => $parties->filter(fn($p) => in_array($p->relationship_type, ['data_processor','data_controller','joint_controller']) && $p->dpa_status === 'none')->count(),
        ];

        // Data flows for the map (each party + their data types)
        $flows = $parties->map(fn($p) => [
            'id'          => $p->id,
            'name'        => $p->name,
            'code'        => $p->code,
            'type'        => $p->relationship_type,
            'risk'        => $p->risk_level,
            'cross'       => $p->is_cross_border,
            'countries'   => $p->transfer_countries ?? [],
            'data_types'  => $p->data_types_shared ?? [],
            'dpa_status'  => $p->dpa_status,
            'country'     => $p->country,
            'status'      => $p->status,
        ]);

        // ROPA-linked parties for flow detail
        $ropaLinks = \DB::table('ropa_external_parties as rep')
            ->join('ropa_records as r', 'rep.ropa_record_id','=','r.id')
            ->join('external_parties as ep', 'rep.external_party_id','=','ep.id')
            ->select('ep.id as party_id','ep.name as party_name','ep.relationship_type',
                     'r.process_name','rep.party_role','rep.data_categories')
            ->get();

        return view('modules.data-map.index', compact('org','grouped','stats','flows','ropaLinks'));
    }
}
