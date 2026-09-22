<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogApiRequests
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
        $start = now();

        $response = $next($request);

        $duration = now()->diffInMilliseconds($start);

        $logData = [
            'method' => $request->getMethod(),
            'url' => $request->getPathInfo(),
            'status' => $response->status(),
            'duration_ms' => $duration,
            'user_id' => auth()->id(),
            'ip' => $request->ip(),
        ];

        if ($response->status() >= 400) {
            Log::warning('API Request Error', $logData);
        } else {
            Log::info('API Request Success', $logData);
        }

        return $response;
    }
}
