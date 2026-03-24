<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\BreachIncident;
use App\Models\Consent;
use App\Models\RightsRequest;
use App\Models\TrainingCompletion;
use App\Models\TrainingCourse;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $orgId = $user->organization_id;

        // Key metrics
        $activeConsents = Consent::where('organization_id', $orgId)->active()->count();
        $pendingRights  = RightsRequest::where('organization_id', $orgId)->pending()->count();
        $overdueRights  = RightsRequest::where('organization_id', $orgId)->overdue()->count();
        $openBreaches   = BreachIncident::where('organization_id', $orgId)
            ->whereNotIn('status', ['resolved', 'closed'])->count();

        // Urgent breaches (not resolved, deadline approaching within 72h)
        $urgentBreaches = BreachIncident::where('organization_id', $orgId)
            ->whereNotIn('status', ['resolved', 'closed', 'notified'])
            ->whereNotNull('pdpc_notification_deadline')
            ->where('pdpc_notification_deadline', '>', now())
            ->orderBy('pdpc_notification_deadline')
            ->get();

        // Recent rights requests
        $recentRights = RightsRequest::where('organization_id', $orgId)
            ->latest()
            ->take(5)
            ->get();

        // Recent breaches
        $recentBreaches = BreachIncident::where('organization_id', $orgId)
            ->latest()
            ->take(5)
            ->get();

        // Compliance score (simple calculation)
        $org = $user->organization;
        $complianceScore = $org->getComplianceScore();

        // Training stats
        $totalCourses   = TrainingCourse::where('organization_id', $orgId)->count();
        $userCompleted  = TrainingCompletion::where('user_id', $user->id)->where('passed', true)->count();

        return view('modules.dashboard.index', compact(
            'activeConsents',
            'pendingRights',
            'overdueRights',
            'openBreaches',
            'urgentBreaches',
            'recentRights',
            'recentBreaches',
            'complianceScore',
            'totalCourses',
            'userCompleted'
        ));
    }
}
