<?php

namespace App\Http\Middleware;

use App\Models\OperationLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OperationLogMiddleware
{
    private const SKIP_ROUTES = [
        'debugbar.*', '_ignition.*', 'horizon.*', 'telescope.*',
    ];

    private const SKIP_PATTERNS = [
        '/_debugbar', '/_ignition', '/favicon', '/up',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    public function terminate(Request $request, Response $response): void
    {
        // Skip non-essential paths
        $path = $request->path();
        foreach (self::SKIP_PATTERNS as $p) {
            if (str_starts_with('/'.$path, $p)) return;
        }

        // Skip asset requests
        if (preg_match('/\.(css|js|png|jpg|gif|ico|svg|woff|woff2|ttf)$/i', $path)) return;

        try {
            $routeName   = $request->route()?->getName();
            $routeAction = $request->route()?->getActionName();
            $user        = $request->user();

            $startTime   = defined('LARAVEL_START') ? LARAVEL_START : microtime(true);
            $durationMs  = (int)((microtime(true) - $startTime) * 1000);
            $memoryMb    = (int)(memory_get_peak_usage(true) / 1024 / 1024);

            OperationLog::create([
                'organization_id' => $user?->organization_id,
                'user_id'         => $user?->id,
                'user_name'       => $user?->name,
                'method'          => $request->method(),
                'url'             => substr($request->fullUrl(), 0, 500),
                'route_name'      => $routeName ? substr($routeName, 0, 150) : null,
                'route_action'    => $routeAction ? substr($routeAction, 0, 150) : null,
                'status_code'     => $response->getStatusCode(),
                'duration_ms'     => $durationMs,
                'memory_mb'       => $memoryMb,
                'request_size'    => (int)strlen($request->getContent()),
                'response_size'   => (int)strlen($response->getContent()),
                'ip_address'      => $request->ip(),
                'session_id'      => $request->hasSession() ? substr($request->session()->getId(), 0, 100) : null,
                'user_agent'      => substr($request->userAgent() ?? '', 0, 500),
                'referer'         => $request->headers->get('referer') ? substr($request->headers->get('referer'), 0, 500) : null,
                'created_at'      => now(),
            ]);
        } catch (\Throwable) {
            // ไม่ให้ logging error กระทบ request หลัก
        }
    }
}
