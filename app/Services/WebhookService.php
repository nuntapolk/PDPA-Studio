<?php

namespace App\Services;

use App\Models\Webhook;
use App\Models\WebhookLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebhookService
{
    public function dispatch(Webhook $webhook, string $event, array $payload): void
    {
        $body = json_encode([
            'event' => $event,
            'timestamp' => now()->toIso8601String(),
            'organization_id' => $webhook->organization_id,
            'data' => $payload,
        ]);

        // HMAC Signature
        $signature = hash_hmac('sha256', $body, $webhook->secret);

        $headers = array_merge(
            $webhook->headers ?? [],
            [
                'Content-Type' => 'application/json',
                'X-PDPA-Signature' => 'sha256=' . $signature,
                'X-PDPA-Event' => $event,
                'User-Agent' => 'PDPA-Studio/1.0',
            ]
        );

        $startTime = microtime(true);

        try {
            $response = Http::withHeaders($headers)
                ->timeout(10)
                ->retry(3, 1000)
                ->post($webhook->url, json_decode($body, true));

            $duration = (int)((microtime(true) - $startTime) * 1000);
            $success = $response->successful();

            WebhookLog::create([
                'webhook_id' => $webhook->id,
                'event' => $event,
                'payload' => json_decode($body, true),
                'response_status' => $response->status(),
                'response_body' => substr($response->body(), 0, 500),
                'duration_ms' => $duration,
                'success' => $success,
                'triggered_at' => now(),
            ]);

            $webhook->increment($success ? 'success_count' : 'failure_count');
            $webhook->update([
                'last_triggered_at' => now(),
                'last_error' => $success ? null : 'HTTP ' . $response->status(),
            ]);

        } catch (\Exception $e) {
            $duration = (int)((microtime(true) - $startTime) * 1000);

            WebhookLog::create([
                'webhook_id' => $webhook->id,
                'event' => $event,
                'payload' => json_decode($body, true),
                'success' => false,
                'duration_ms' => $duration,
                'triggered_at' => now(),
            ]);

            $webhook->increment('failure_count');
            $webhook->update(['last_error' => $e->getMessage()]);

            Log::error("Webhook failed: {$webhook->url} — {$e->getMessage()}");
        }
    }

    public function dispatchToOrg(int $orgId, string $event, array $payload): void
    {
        $webhooks = Webhook::where('organization_id', $orgId)
            ->where('is_active', true)
            ->whereJsonContains('events', $event)
            ->get();

        foreach ($webhooks as $webhook) {
            $this->dispatch($webhook, $event, $payload);
        }
    }
}
