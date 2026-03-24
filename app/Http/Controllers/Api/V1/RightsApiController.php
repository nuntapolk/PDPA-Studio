<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\RightsRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Tag(name="Rights Requests", description="Data Subject Rights API")
 */
class RightsApiController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/v1/rights/requests",
     *     summary="ยื่นคำร้องสิทธิ์",
     *     tags={"Rights Requests"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(response=201, description="Created")
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:access,rectification,erasure,restriction,portability,objection',
            'requester_name' => 'required|string|max:255',
            'requester_email' => 'required|email',
            'requester_phone' => 'nullable|string',
            'description' => 'required|string|max:2000',
        ]);

        $orgId = $request->user()->organization_id ?? $request->get('_org_id');
        $req = RightsRequest::create(array_merge($validated, [
            'organization_id' => $orgId,
            'status' => 'pending',
            'submitted_at' => now(),
            'due_date' => now()->addDays(30),
        ]));

        return response()->json([
            'success' => true,
            'data' => [
                'ticket_number' => $req->ticket_number,
                'type' => $req->type,
                'status' => $req->status,
                'due_date' => $req->due_date,
            ]
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/rights/requests/{ticket}",
     *     summary="ตรวจสอบสถานะคำร้อง",
     *     tags={"Rights Requests"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="ticket", in="path", required=true),
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function status(Request $request, string $ticket): JsonResponse
    {
        $orgId = $request->user()->organization_id ?? $request->get('_org_id');
        $req = RightsRequest::where('organization_id', $orgId)
            ->where('ticket_number', $ticket)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => [
                'ticket_number' => $req->ticket_number,
                'type' => $req->getTypeLabel(),
                'status' => $req->getStatusLabel(),
                'submitted_at' => $req->submitted_at,
                'due_date' => $req->due_date,
                'days_remaining' => $req->days_remaining,
                'completed_at' => $req->completed_at,
            ]
        ]);
    }
}
