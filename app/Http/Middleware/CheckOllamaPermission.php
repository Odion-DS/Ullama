<?php

namespace App\Http\Middleware;

use App\Enums\OllamaPermission;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckOllamaPermission
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        /** @var \App\Models\OllamaToken $ollamaToken */
        $ollamaToken = $request->user();

        $permissionEnum = OllamaPermission::tryFrom($permission);

        if (!$permissionEnum || !$ollamaToken->canApiCall($permissionEnum)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
