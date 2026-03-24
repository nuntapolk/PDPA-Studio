<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\BreachIncident;
use App\Models\Consent;
use App\Models\RightsRequest;
use App\Models\RopaRecord;
use App\Models\TrainingCompletion;
use App\Models\TrainingCourse;
use App\Models\Vendor;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $org = $request->user()->organization;

        // Key Metrics
        $metrics = [
            'compliance_score' => $org->getComplianceScore(),
            'active_consents' => Consent::where('organization_id', $org->id)->active()->count(),
            'pending_rights' => RightsRequest::where('organization_id', $org->id)->pending()->count(),
            'overdue_rights' => RightsRequest::where('organization_id', $org->id)->overdue()->count(),
            'open_breaches' => BreachIncident::where('organization_id', $org->id)
                ->whereNotIn('status', ['resolved', 'closed'])->count(),
            'critical_breaches' => BreachIncident::where('organization_id', $org->id)
                ->where('severity', 'critical')
                ->whereNotIn('status', ['resolved', 'closed'])->count(),
            'ropa_count' => RopaRecord::where('organization_id', $org->id)->where('status', 'active')->count(),
            'vendors_without_dpa' => Vendor::where('organization_id', $org->id)
                ->where('status', 'active')
                ->where(function ($q) {
                    $q->where('dpa_signed', false)
                        ->orWhereDate('dpa_expires_at', '<', now());
                })->count(),
        ];

        // Breach Countdown (Critical breaches nearing PDPC deadline)
        $urgentBreaches = BreachIncident::where('organization_id', $org->id)
            ->whereNull('pdpc_notified_at')
            ->where('requires_pdpc_notification', true)
            ->whereNotNull('pdpc_notification_deadline')
            ->where('pdpc_notification_deadline', '>', now())
            ->get();

        // Training Completion Rate
        $totalUsers = $org->users()->count();
        $requiredCourses = TrainingCourse::where('organization_id', $org->id)
            ->where('is_required', true)->count();
        $trainingStats = [
            'required_courses' => $requiredCourses,
            'completion_rate' => $totalUsers > 0 && $requiredCourses > 0
                ? round(TrainingCompletion::whereIn('course_id',
                    TrainingCourse::where('organization_id', $org->id)->where('is_required', true)->pluck('id'))
                    ->where('passed', true)->distinct('user_id')->count() / $totalUsers * 100, 1)
                : 0,
        ];

        // Upcoming tasks / deadlines
        $upcomingRights = RightsRequest::where('organization_id', $org->id)
            ->pending()
            ->orderBy('due_date')
            ->limit(5)
            ->get();

        // DPA expiring vendors
        $expiringDpa = Vendor::where('organization_id', $org->id)
            ->where('dpa_signed', true)
            ->where('dpa_expires_at', '<=', now()->addDays(60))
            ->where('dpa_expires_at', '>', now())
            ->orderBy('dpa_expires_at')
            ->get();

        // Recent activity
        $recentActivity = AuditLog::where('organization_id', $org->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Monthly consent trend (last 6 months)
        $consentTrend = Consent::where('organization_id', $org->id)
            ->where('granted_at', '>=', now()->subMonths(6))
            ->selectRaw('DATE_FORMAT(granted_at, "%Y-%m") as month, COUNT(*) as count, SUM(granted) as granted')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('modules.dashboard.index', compact(
            'org', 'metrics', 'urgentBreaches', 'trainingStats',
            'upcomingRights', 'expiringDpa', 'recentActivity', 'consentTrend'
        ));
    }
}
