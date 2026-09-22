<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiter;

class ApiRateLimit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $limit
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $limit = '60,1')
    {
        // Parse the limit format: requests,minutes
        list($requestLimit, $minutes) = explode(',', $limit);
        $minutes = (int) $minutes ?: 1;

        // Use user ID if authenticated, otherwise use IP
        $key = auth()->check() 
            ? 'api:' . auth()->id() 
            : 'api:' . $request->ip();

        $limiter = app(RateLimiter::class);

        if ($limiter->tooManyAttempts($key, $requestLimit)) {
            $retryAfter = $limiter->availableIn($key);
            return response()->json([
                'message' => 'Too many requests',
                'retry_after' => $retryAfter,
            ], 429)
            ->header('Retry-After', $retryAfter);
        }

        $limiter->hit($key, $minutes * 60);

        return $next($request);
    }
}
