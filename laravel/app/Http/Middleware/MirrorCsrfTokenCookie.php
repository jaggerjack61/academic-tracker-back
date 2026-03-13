<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;

class MirrorCsrfTokenCookie
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->headers->has('X-CSRFToken') && ! $request->headers->has('X-CSRF-TOKEN')) {
            $request->headers->set('X-CSRF-TOKEN', (string) $request->headers->get('X-CSRFToken'));
        }

        $response = $next($request);
        $csrfToken = csrf_token();

        if ($request->cookies->get('csrftoken') !== $csrfToken) {
            $response->headers->setCookie(new Cookie(
                'csrftoken',
                $csrfToken,
                0,
                config('session.path', '/'),
                config('session.domain'),
                (bool) config('session.secure', false),
                false,
                false,
                config('session.same_site', 'lax')
            ));
        }

        return $response;
    }
}
