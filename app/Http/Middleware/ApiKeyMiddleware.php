<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Psy\Readline\Hoa\Console;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get the API key from the request header
        $apiKey = $request->header('X-API-Key');

        // Check if the API key matches the expected key from the environment
        if (!$apiKey || $apiKey !== env('API_KEY')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }


        return $next($request);
    }
}
