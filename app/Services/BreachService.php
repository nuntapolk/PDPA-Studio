<?php

namespace App\Services;

use App\Models\BreachIncident;
use App\Models\BreachTimeline;
use App\Models\Webhook;
use Illuminate\Support\Carbon;

class BreachService
{
    public function createIncident(int $orgId, int $userId, array $data): BreachIncident
    {
        $discoveredAt = Carbon::parse($data['discovered_at']);

        $incident = BreachIncident::create([
            'organization_id' => $orgId,
            'title' => $data['title'],
            'description' => $data['description'],
            'breach_type' => $data['breach_type'],
            'severity' => $data['severity'],
            'status' => 'new',
            'discovered_at' => $discoveredAt,
            'occurred_at' => isset($data['occurred_at']) ? Carbon::parse($data['occurred_at']) : null,
            'affected_count' => $data['affected_count'],
            'data_types_affected' => $data['data_types_affected'],
            'includes_sensitive_data' => $data['includes_sensitive_data'] ?? false,
            'impact_assessment' => $data['impact_assessment'] ?? null,
            'requires_pdpc_notification' => $data['requires_pdpc_notification'] ?? ($data['affected_count'] > 0),
            'pdpc_notification_deadline' => $discoveredAt->addHours(72),
            'requires_subject_notification' => $data['requires_subject_notification'] ?? false,
            'reported_by' => $userId,
        ]);

        // Add initial timeline entry
        BreachTimeline::create([
            'breach_incident_id' => $incident->id,
            'user_id' => $userId,
            'action' => 'รายงานเหตุการณ์',
            'description' => 'เริ่มบันทึกเหตุการณ์ละเมิดข้อมูลส่วนบุคคล — PDPC deadline: ' . $incident->pdpc_notification_deadline?->format('d/m/Y H:i'),
        ]);

        // Fire Webhook
        $this->fireWebhook($orgId, 'breach.reported', $incident);

        // If critical, dispatch urgent notification job
        if ($incident->severity === 'critical') {
            // dispatch(new SendBreachNotification($incident)); // uncomment when queue is set up
        }

        return $incident;
    }

    public function fireWebhook(int $orgId, string $event, BreachIncident $incident): void
    {
        $webhooks = Webhook::where('organization_id', $orgId)
            ->where('is_active', true)
            ->whereJsonContains('events', $event)
            ->get();

        foreach ($webhooks as $webhook) {
            // dispatch(new FireWebhookJob($webhook, $event, $incident->toArray()));
            // ในการใช้จริงให้ใช้ Queue Job
        }
    }

    public function checkDeadlines(): array
    {
        $approaching = BreachIncident::whereNull('pdpc_notified_at')
            ->where('requires_pdpc_notification', true)
            ->where('pdpc_notification_deadline', '<=', now()->addHours(24))
            ->where('pdpc_notification_deadline', '>', now())
            ->get();

        $overdue = BreachIncident::whereNull('pdpc_notified_at')
            ->where('requires_pdpc_notification', true)
            ->where('pdpc_notification_deadline', '<', now())
            ->get();

        return ['approaching' => $approaching, 'overdue' => $overdue];
    }
}
