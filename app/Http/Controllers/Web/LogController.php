<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\ConsentEventLog;
use App\Models\DataAccessLog;
use App\Models\OperationLog;
use App\Models\SecurityLog;
use App\Models\SystemErrorLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogController extends Controller
{
    private function requireAdmin(): void
    {
        abort_unless(Auth::user()->isAdmin(), 403, 'เฉพาะผู้ดูแลระบบเท่านั้น');
    }

    // ── Index (Audit Log — default tab) ───────────────────────────────────────
    public function index(Request $request)
    {
        $this->requireAdmin();
        $orgId   = Auth::user()->organization_id;
        $perPage = in_array((int)$request->per_page, [50, 100, 200]) ? (int)$request->per_page : 50;

        $query = AuditLog::where('organization_id', $orgId)->with('user');

        if ($request->filled('module'))  $query->where('module', $request->module);
        if ($request->filled('action'))  $query->where('action', $request->action);
        if ($request->filled('user_id')) $query->where('user_id', $request->user_id);
        if ($request->filled('date_from')) $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->filled('date_to'))   $query->whereDate('created_at', '<=', $request->date_to);
        if ($request->filled('search'))    $query->where(function($q) use ($request) {
            $q->where('entity_name', 'like', '%'.$request->search.'%')
              ->orWhere('url', 'like', '%'.$request->search.'%')
              ->orWhere('user_name', 'like', '%'.$request->search.'%');
        });

        $logs    = $query->orderByDesc('created_at')->paginate($perPage)->withQueryString();

        // Stats (24h)
        $stats = [
            'total_today'    => AuditLog::where('organization_id', $orgId)->whereDate('created_at', today())->count(),
            'unique_users'   => AuditLog::where('organization_id', $orgId)->whereDate('created_at', today())->distinct('user_id')->count('user_id'),
            'actions_by_type'=> AuditLog::where('organization_id', $orgId)->selectRaw('action, COUNT(*) as cnt')->groupBy('action')->orderByDesc('cnt')->limit(5)->pluck('cnt','action'),
        ];

        $modules  = AuditLog::where('organization_id', $orgId)->distinct()->pluck('module')->sort()->values();
        $actions  = AuditLog::where('organization_id', $orgId)->distinct()->pluck('action')->sort()->values();

        return view('modules.logs.index', compact('logs', 'stats', 'modules', 'actions', 'perPage'));
    }

    // ── Operation Log ─────────────────────────────────────────────────────────
    public function operation(Request $request)
    {
        $this->requireAdmin();
        $orgId   = Auth::user()->organization_id;
        $perPage = in_array((int)$request->per_page, [50, 100, 200]) ? (int)$request->per_page : 50;

        $query = OperationLog::where('organization_id', $orgId);

        if ($request->filled('method'))     $query->where('method', $request->method_filter);
        if ($request->filled('status'))     $query->where('status_code', $request->status);
        if ($request->filled('slow'))       $query->where('duration_ms', '>=', (int)$request->slow);
        if ($request->filled('route'))      $query->where('route_name', 'like', '%'.$request->route.'%');
        if ($request->filled('date_from'))  $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->filled('date_to'))    $query->whereDate('created_at', '<=', $request->date_to);

        $logs = $query->orderByDesc('created_at')->paginate($perPage)->withQueryString();

        // Stats
        $stats = [
            'avg_duration'   => (int) OperationLog::where('organization_id', $orgId)->whereDate('created_at', today())->avg('duration_ms'),
            'total_requests' => OperationLog::where('organization_id', $orgId)->whereDate('created_at', today())->count(),
            'error_rate'     => OperationLog::where('organization_id', $orgId)->whereDate('created_at', today())->where('status_code', '>=', 400)->count(),
            'slow_requests'  => OperationLog::where('organization_id', $orgId)->whereDate('created_at', today())->where('duration_ms', '>', 1000)->count(),
            'top_routes'     => OperationLog::where('organization_id', $orgId)
                                    ->selectRaw('route_name, COUNT(*) as cnt, AVG(duration_ms) as avg_ms')
                                    ->whereDate('created_at', today())
                                    ->whereNotNull('route_name')
                                    ->groupBy('route_name')
                                    ->orderByDesc('cnt')
                                    ->limit(10)
                                    ->get(),
        ];

        return view('modules.logs.operation', compact('logs', 'stats', 'perPage'));
    }

    // ── Security Log ──────────────────────────────────────────────────────────
    public function security(Request $request)
    {
        $this->requireAdmin();
        $orgId   = Auth::user()->organization_id;
        $perPage = in_array((int)$request->per_page, [50, 100, 200]) ? (int)$request->per_page : 50;

        $query = SecurityLog::where('organization_id', $orgId);

        if ($request->filled('severity'))   $query->where('severity', $request->severity);
        if ($request->filled('event_type')) $query->where('event_type', $request->event_type);
        if ($request->boolean('unresolved')) $query->where('is_resolved', false);
        if ($request->filled('date_from'))  $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->filled('date_to'))    $query->whereDate('created_at', '<=', $request->date_to);

        $logs = $query->orderByDesc('created_at')->paginate($perPage)->withQueryString();

        $stats = [
            'critical_unresolved' => SecurityLog::where('organization_id', $orgId)->where('severity','critical')->where('is_resolved',false)->count(),
            'high_unresolved'     => SecurityLog::where('organization_id', $orgId)->where('severity','high')->where('is_resolved',false)->count(),
            'login_failed_today'  => SecurityLog::where('organization_id', $orgId)->where('event_type','login_failed')->whereDate('created_at',today())->count(),
            'by_severity'         => SecurityLog::where('organization_id', $orgId)->selectRaw('severity, COUNT(*) as cnt')->groupBy('severity')->pluck('cnt','severity'),
        ];

        return view('modules.logs.security', compact('logs', 'stats', 'perPage'));
    }

    // ── Data Access Log ───────────────────────────────────────────────────────
    public function dataAccess(Request $request)
    {
        $this->requireAdmin();
        $orgId   = Auth::user()->organization_id;
        $perPage = in_array((int)$request->per_page, [50, 100, 200]) ? (int)$request->per_page : 50;

        $query = DataAccessLog::where('organization_id', $orgId)->with('user');

        if ($request->filled('access_type'))   $query->where('access_type', $request->access_type);
        if ($request->filled('data_category')) $query->where('data_category', $request->data_category);
        if ($request->boolean('cross_border')) $query->where('is_cross_border', true);
        if ($request->filled('date_from'))     $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->filled('date_to'))       $query->whereDate('created_at', '<=', $request->date_to);

        $logs = $query->orderByDesc('created_at')->paginate($perPage)->withQueryString();

        $stats = [
            'exports_today'       => DataAccessLog::where('organization_id', $orgId)->whereDate('created_at',today())->where('access_type','export')->count(),
            'cross_border_month'  => DataAccessLog::where('organization_id', $orgId)->whereMonth('created_at',now()->month)->where('is_cross_border',true)->count(),
            'sensitive_access'    => DataAccessLog::where('organization_id', $orgId)->whereDate('created_at',today())->whereIn('data_category',['sensitive','health','biometric','financial'])->count(),
            'by_category'         => DataAccessLog::where('organization_id', $orgId)->selectRaw('data_category, COUNT(*) as cnt')->groupBy('data_category')->orderByDesc('cnt')->pluck('cnt','data_category'),
        ];

        return view('modules.logs.data-access', compact('logs', 'stats', 'perPage'));
    }

    // ── Consent Event Log ─────────────────────────────────────────────────────
    public function consentEvents(Request $request)
    {
        $this->requireAdmin();
        $orgId   = Auth::user()->organization_id;
        $perPage = in_array((int)$request->per_page, [50, 100, 200]) ? (int)$request->per_page : 50;

        $query = ConsentEventLog::where('organization_id', $orgId);

        if ($request->filled('event_type')) $query->where('event_type', $request->event_type);
        if ($request->filled('channel'))    $query->where('channel', $request->channel);
        if ($request->filled('search'))     $query->where(function($q) use ($request) {
            $q->where('data_subject_name','like','%'.$request->search.'%')
              ->orWhere('data_subject_email','like','%'.$request->search.'%');
        });
        if ($request->filled('date_from'))  $query->whereDate('event_at', '>=', $request->date_from);
        if ($request->filled('date_to'))    $query->whereDate('event_at', '<=', $request->date_to);

        $logs = $query->orderByDesc('event_at')->paginate($perPage)->withQueryString();

        $stats = [
            'granted_month'   => ConsentEventLog::where('organization_id', $orgId)->whereMonth('event_at',now()->month)->where('event_type','granted')->count(),
            'withdrawn_month' => ConsentEventLog::where('organization_id', $orgId)->whereMonth('event_at',now()->month)->where('event_type','withdrawn')->count(),
            'by_event'        => ConsentEventLog::where('organization_id', $orgId)->selectRaw('event_type, COUNT(*) as cnt')->groupBy('event_type')->pluck('cnt','event_type'),
            'by_channel'      => ConsentEventLog::where('organization_id', $orgId)->selectRaw('channel, COUNT(*) as cnt')->groupBy('channel')->pluck('cnt','channel'),
        ];

        return view('modules.logs.consent-events', compact('logs', 'stats', 'perPage'));
    }

    // ── System Error Log ──────────────────────────────────────────────────────
    public function errors(Request $request)
    {
        $this->requireAdmin();
        $perPage = in_array((int)$request->per_page, [50, 100, 200]) ? (int)$request->per_page : 50;

        $query = SystemErrorLog::query();

        if ($request->filled('level'))      $query->where('level', $request->level);
        if ($request->filled('channel'))    $query->where('channel', $request->channel);
        if ($request->boolean('unresolved')) $query->where('is_resolved', false);
        if ($request->filled('search'))     $query->where('message','like','%'.$request->search.'%');
        if ($request->filled('date_from'))  $query->whereDate('created_at', '>=', $request->date_from);
        if ($request->filled('date_to'))    $query->whereDate('created_at', '<=', $request->date_to);

        $logs = $query->orderByDesc('created_at')->paginate($perPage)->withQueryString();

        $stats = [
            'critical_unresolved' => SystemErrorLog::where('level','critical')->where('is_resolved',false)->count(),
            'errors_today'        => SystemErrorLog::whereDate('created_at',today())->count(),
            'by_level'            => SystemErrorLog::selectRaw('level, COUNT(*) as cnt')->groupBy('level')->pluck('cnt','level'),
        ];

        return view('modules.logs.errors', compact('logs', 'stats', 'perPage'));
    }

    // ── Mark Security Log Resolved ────────────────────────────────────────────
    public function resolveSecurityLog(Request $request, SecurityLog $log)
    {
        $this->requireAdmin();
        $log->update([
            'is_resolved' => true,
            'resolved_at' => now(),
            'resolved_by' => Auth::user()->name,
        ]);
        return back()->with('success', 'บันทึกว่าแก้ไขแล้ว');
    }

    // ── Mark Error Resolved ───────────────────────────────────────────────────
    public function resolveError(Request $request, SystemErrorLog $log)
    {
        $this->requireAdmin();
        $log->update([
            'is_resolved'     => true,
            'resolved_at'     => now(),
            'resolved_by'     => Auth::user()->name,
            'resolution_note' => $request->note,
        ]);
        return back()->with('success', 'บันทึกว่าแก้ไขแล้ว');
    }
}
