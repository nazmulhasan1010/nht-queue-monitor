<?php

namespace NHT\QueueMonitor\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class QueueMonitorAccess
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (! $user) {
            abort(403, 'Unauthorized.');
        }

        $allowedEmails = config('queue-monitor.access.allowed_emails', []);

        if (! empty($allowedEmails) && in_array($user->email, $allowedEmails, true)) {
            return $next($request);
        }

        if (config('queue-monitor.access.enable_gate', false)) {
            $gate = config('queue-monitor.access.gate', 'viewQueueMonitor');

            if (Gate::allows($gate)) {
                return $next($request);
            }

            abort(403, 'You are not allowed to access Queue Pulse.');
        }

        if (empty($allowedEmails)) {
            return $next($request);
        }

        abort(403, 'You are not allowed to access Queue Pulse.');
    }
}
