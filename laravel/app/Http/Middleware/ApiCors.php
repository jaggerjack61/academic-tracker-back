<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiCors
{
    public function handle(Request $request, Closure $next): Response
    {
        $origin = $request->headers->get('Origin', 'http://localhost:3000');

        if ($request->isMethod('OPTIONS') && $request->is('api/*')) {
            $response = response()->noContent();
        } else {
            $response = $next($request);
        }

        if ($request->is('api/*')) {
            $response->headers->set('Access-Control-Allow-Origin', $origin);
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
            $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
            $response->headers->set('Access-Control-Allow-Headers', 'Accept, Authorization, Content-Type, Origin, X-CSRF-TOKEN, X-CSRFToken, X-Requested-With');
            $response->headers->set('Vary', 'Origin');
        }

        return $response;
    }
}