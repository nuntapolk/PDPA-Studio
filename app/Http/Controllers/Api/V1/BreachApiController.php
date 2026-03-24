<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\BreachIncident;
use App\Services\BreachService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Tag(name="Breach", description="Data Breach Management API")
 */
class BreachApiController extends Controller
{
    public function __construct(private BreachService $breachService) {}

    /**
     * @OA\Post(
     *     path="/api/v1/breach/report",
     *     summary="รายงานเหตุละเมิดข้อมูล",
     *     tags={"Breach"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=201, description="Created — 72hr timer started")
     * )
     */
    public function report(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'breach_type' => 'required|string',
            'severity' => 'required|in:low,medium,high,critical',
            'discovered_at' => 'required|date',
            'affected_count' => 'required|integer|min:0',
            'data_types_affected' => 'required|array',
            'includes_sensitive_data' => 'boolean',
            'requires_pdpc_notification' => 'boolean',
        ]);

        $orgId = $request->user()->organization_id ?? $request->get('_org_id');
        $userId = $request->user()->id ?? $request->get('_user_id', 1);

        $incident = $this->breachService->createIncident($orgId, $userId, $validated);

        return response()->json([
            'success' => true,
            'data' => [
                'incident_number' => $incident->incident_number,
                'severity' => $incident->severity,
                'status' => $incident->status,
                'pdpc_notification_deadline' => $incident->pdpc_notification_deadline,
                'hours_until_deadline' => $incident->hours_until_deadline,
            ]
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/breach",
     *     summary="รายการเหตุการณ์ทั้งหมด",
     *     tags={"Breach"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $orgId = $request->user()->organization_id ?? $request->get('_org_id');
        $incidents = BreachIncident::where('organization_id', $orgId)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json(['success' => true, 'data' => $incidents]);
    }
}
