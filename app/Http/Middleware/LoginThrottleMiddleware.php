<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LoginThrottleMiddleware
{
    private const MAX_ATTEMPTS = 5;
    private const LOCKOUT_MINUTES = 15;

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('POST') && $request->routeIs('login')) {
            $user = User::where('email', $request->input('email'))->first();

            if ($user && $user->failed_attempts >= self::MAX_ATTEMPTS) {
                $lockoutUntil = $user->last_failed_login?->addMinutes(self::LOCKOUT_MINUTES);

                if ($lockoutUntil && now()->lt($lockoutUntil)) {
                    $remaining = (int) now()->diffInMinutes($lockoutUntil, false);
                    return back()->withErrors([
                        'email' => "Account locked. Try again in {$remaining} minute(s).",
                    ])->withInput($request->only('email'));
                }

                // Lockout period expired — reset counter
                $user->update(['failed_attempts' => 0, 'last_failed_login' => null]);
            }
        }

        return $next($request);
    }
}
