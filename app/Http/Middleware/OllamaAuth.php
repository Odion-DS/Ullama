<?php

namespace App\Http\Middleware;

use App\Models\OllamaToken;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class OllamaAuth
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Extrahiere Bearer Token aus dem Authorization Header
        $token = $request->bearerToken();

        if (empty($token)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Get all valid tokens and check hash
        $ollamaToken = OllamaToken::query()->valid()->get()->first(function ($dbToken) use ($token) {
            return Hash::check($token, $dbToken->token);
        });

        if (is_null($ollamaToken)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if ($ollamaToken->isExpired()) {
            return response()->json(['message' => 'Token expired'], 401);
        }

        auth()->login($ollamaToken);
        
        return $next($request);
    }
}
