<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $roles =  explode("|", $role);

//        dd($roles,$role);
        if(!in_array($request->user()->role->name, $roles)) {
            return response('Your User Role is not authorized to view this page.',403);
        }
        return $next($request);
    }
}
