<?php

namespace App\Services;

use App\Models\Consent;
use App\Models\ConsentTemplate;
use App\Models\DataSubject;
use App\Models\Webhook;

class ConsentService
{
    public function grantConsent(
        int $orgId,
        DataSubject $subject,
        ConsentTemplate $template,
        string $channel = 'web',
        string $ipAddress = null,
        array $metadata = []
    ): Consent {
        // ถอนความยินยอมเก่าก่อน (ถ้ามี)
        Consent::where('data_subject_id', $subject->id)
            ->where('template_id', $template->id)
            ->whereNull('withdrawn_at')
            ->update(['withdrawn_at' => now(), 'withdrawal_reason' => 'เปลี่ยนแปลงโดยอัตโนมัติเมื่อมีการให้ความยินยอมใหม่']);

        $consent = Consent::create([
            'organization_id' => $orgId,
            'data_subject_id' => $subject->id,
            'template_id' => $template->id,
            'template_version' => $template->version,
            'channel' => $channel,
            'granted' => true,
            'ip_address' => $ipAddress,
            'granted_at' => now(),
            'expires_at' => $template->retention_days ? now()->addDays($template->retention_days) : null,
            'metadata' => $metadata ?: null,
        ]);

        $this->fireWebhook($orgId, 'consent.granted', $consent);
        return $consent;
    }

    public function withdrawConsent(Consent $consent, string $reason = null): void
    {
        $consent->update([
            'withdrawn_at' => now(),
            'withdrawal_reason' => $reason,
        ]);

        $this->fireWebhook($consent->organization_id, 'consent.withdrawn', $consent);
    }

    private function fireWebhook(int $orgId, string $event, Consent $consent): void
    {
        $webhooks = Webhook::where('organization_id', $orgId)
            ->where('is_active', true)
            ->whereJsonContains('events', $event)
            ->get();

        foreach ($webhooks as $webhook) {
            // dispatch(new FireWebhookJob($webhook, $event, $consent->toArray()));
        }
    }

    public function getConsentStats(int $orgId): array
    {
        return [
            'total' => Consent::where('organization_id', $orgId)->count(),
            'active' => Consent::where('organization_id', $orgId)->active()->count(),
            'withdrawn' => Consent::where('organization_id', $orgId)->whereNotNull('withdrawn_at')->count(),
            'expiring_soon' => Consent::where('organization_id', $orgId)->expiringSoon(30)->count(),
            'expired' => Consent::where('organization_id', $orgId)
                ->where('granted', true)
                ->whereNotNull('expires_at')
                ->where('expires_at', '<', now())->count(),
        ];
    }
}
