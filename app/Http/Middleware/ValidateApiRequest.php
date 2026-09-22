<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ValidateApiRequest
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
        // Validate Content-Type for POST/PUT/PATCH requests with body
        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH']) && $request->getContent()) {
            $contentType = $request->header('Content-Type');
            
            if (!$contentType || !str_contains($contentType, 'application/json')) {
                return response()->json([
                    'message' => 'Content-Type must be application/json',
                ], 400);
            }
        }

        // Ensure Accept header is set for consistency
        if (!$request->header('Accept')) {
            $request->headers->set('Accept', 'application/json');
        }

        return $next($request);
    }
}
