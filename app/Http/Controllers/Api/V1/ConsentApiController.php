<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Consent;
use App\Models\ConsentTemplate;
use App\Models\DataSubject;
use App\Services\ConsentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Tag(name="Consents", description="Consent Management API")
 */
class ConsentApiController extends Controller
{
    public function __construct(private ConsentService $consentService) {}

    /**
     * @OA\Post(
     *     path="/api/v1/consents",
     *     summary="บันทึก Consent ใหม่",
     *     tags={"Consents"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true,
     *         @OA\JsonContent(
     *             required={"template_id","subject_email","subject_name","granted"},
     *             @OA\Property(property="template_id", type="integer"),
     *             @OA\Property(property="subject_email", type="string"),
     *             @OA\Property(property="subject_name", type="string"),
     *             @OA\Property(property="granted", type="boolean"),
     *             @OA\Property(property="channel", type="string", enum={"web","mobile","paper","verbal","email","api"}),
     *             @OA\Property(property="metadata", type="object")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Created"),
     *     @OA\Response(response=422, description="Validation Error")
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'template_id' => 'required|integer|exists:consent_templates,id',
            'subject_email' => 'required|email',
            'subject_name' => 'required|string|max:255',
            'subject_phone' => 'nullable|string|max:20',
            'subject_reference_id' => 'nullable|string',
            'granted' => 'required|boolean',
            'channel' => 'nullable|in:web,mobile,paper,verbal,email,api',
            'metadata' => 'nullable|array',
        ]);

        $orgId = $request->user()->organization_id ?? $request->get('_org_id');
        $template = ConsentTemplate::where('id', $validated['template_id'])
            ->where('organization_id', $orgId)
            ->where('is_active', true)
            ->firstOrFail();

        // Find or create DataSubject
        $subject = DataSubject::updateOrCreate(
            ['organization_id' => $orgId, 'email' => $validated['subject_email']],
            [
                'first_name' => explode(' ', $validated['subject_name'])[0],
                'last_name' => implode(' ', array_slice(explode(' ', $validated['subject_name']), 1)) ?: '-',
                'phone' => $validated['subject_phone'] ?? null,
                'reference_id' => $validated['subject_reference_id'] ?? null,
                'type' => 'customer',
            ]
        );

        $consent = Consent::create([
            'organization_id' => $orgId,
            'data_subject_id' => $subject->id,
            'template_id' => $template->id,
            'template_version' => $template->version,
            'channel' => $validated['channel'] ?? 'api',
            'granted' => $validated['granted'],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'granted_at' => now(),
            'expires_at' => now()->addDays($template->retention_days),
            'metadata' => $validated['metadata'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'data' => [
                'consent_id' => $consent->id,
                'subject_id' => $subject->id,
                'granted' => $consent->granted,
                'expires_at' => $consent->expires_at,
            ]
        ], 201);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/consents/subject/{email}",
     *     summary="ดู Consent ทั้งหมดของเจ้าของข้อมูล",
     *     tags={"Consents"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="email", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Response(response=200, description="Success")
     * )
     */
    public function getBySubject(Request $request, string $email): JsonResponse
    {
        $orgId = $request->user()->organization_id ?? $request->get('_org_id');
        $subject = DataSubject::where('organization_id', $orgId)->where('email', $email)->first();

        if (!$subject) {
            return response()->json(['success' => false, 'message' => 'ไม่พบเจ้าของข้อมูล'], 404);
        }

        $consents = Consent::where('data_subject_id', $subject->id)
            ->with('template:id,name,purpose,legal_basis')
            ->orderBy('granted_at', 'desc')
            ->get()
            ->map(fn($c) => [
                'id' => $c->id,
                'template' => $c->template->name,
                'purpose' => $c->template->purpose,
                'granted' => $c->granted,
                'status' => $c->getStatusLabel(),
                'channel' => $c->channel,
                'granted_at' => $c->granted_at,
                'withdrawn_at' => $c->withdrawn_at,
                'expires_at' => $c->expires_at,
            ]);

        return response()->json(['success' => true, 'data' => $consents]);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/consents/{id}",
     *     summary="ถอนความยินยอม",
     *     tags={"Consents"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true),
     *     @OA\Response(response=200, description="Withdrawn")
     * )
     */
    public function withdraw(Request $request, int $id): JsonResponse
    {
        $orgId = $request->user()->organization_id ?? $request->get('_org_id');
        $consent = Consent::where('id', $id)->where('organization_id', $orgId)->firstOrFail();

        $consent->update([
            'withdrawn_at' => now(),
            'withdrawal_reason' => $request->input('reason', 'ถอนผ่าน API'),
        ]);

        return response()->json(['success' => true, 'message' => 'ถอนความยินยอมสำเร็จ']);
    }
}
