<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateN8nToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Get the secret token from our .env file
        $secretToken = config('services.n8n.intake_token');

        if (! $secretToken) {
            // If the token isn't set in the .env, fail securely.
            return response()->json(['message' => 'Server misconfigured.'], 500);
        }

        // Check if the 'Authorization' header matches 'Bearer <our_token>'
        if ($request->bearerToken() !== $secretToken) {
            // If not, it's an unauthorized request.
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        // The token is valid. Proceed with the request.
        return $next($request);
    }
}
