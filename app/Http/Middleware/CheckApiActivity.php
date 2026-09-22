<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckApiActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            // Update user's last activity timestamp
            auth()->user()->update([
                'last_activity_at' => now(),
            ]);

            // Check if user account is still active (not blocked)
            if (!auth()->user()->is_active) {
                auth()->logout();
                return response()->json([
                    'message' => 'Your account has been deactivated',
                ], 403);
            }
        }

        return $next($request);
    }
}
