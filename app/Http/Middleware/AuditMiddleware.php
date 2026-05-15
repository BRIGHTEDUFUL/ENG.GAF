<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuditMiddleware
{
    /**
     * Log authenticated page access to audit_logs.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->user() && $request->isMethod('GET') && ! $request->ajax()) {
            try {
                AuditLog::create([
                    'user_id'    => $request->user()->id,
                    'event'      => 'page_access',
                    'new_values' => ['url' => $request->path(), 'method' => $request->method()],
                    'ip_address' => $request->ip(),
                    'user_agent' => substr($request->userAgent() ?? '', 0, 500),
                    'created_at' => now(),
                ]);
            } catch (\Throwable) {
                // Never let audit logging break the request
            }
        }

        return $response;
    }
}
