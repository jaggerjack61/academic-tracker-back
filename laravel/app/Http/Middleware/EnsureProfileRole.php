<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfileRole
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $profile = $request->user()?->profile;

        if (! $profile || ! in_array($profile->type, $roles, true)) {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        return $next($request);
    }
}
