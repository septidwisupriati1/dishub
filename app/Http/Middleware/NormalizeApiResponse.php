<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class NormalizeApiResponse
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
        $response = $next($request);

        // Only modify JSON responses
        if ($response->headers->get('content-type') === 'application/json') {
            $content = json_decode($response->getContent(), true);

            // Check if response is already in standard format
            if (is_array($content) && !isset($content['success'])) {
                // Add success flag based on status code
                $statusCode = $response->getStatusCode();
                $content['success'] = $statusCode >= 200 && $statusCode < 300;
                $content['timestamp'] = now()->toIso8601String();

                $response->setContent(json_encode($content));
            }
        }

        return $response;
    }
}
