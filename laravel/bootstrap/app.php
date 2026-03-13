<?php

use App\Http\Middleware\ApiCors;
use App\Http\Middleware\EnsureProfileRole;
use App\Http\Middleware\MirrorCsrfTokenCookie;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(ApiCors::class);

        $middleware->web(prepend: [
            MirrorCsrfTokenCookie::class,
        ]);

        $middleware->alias([
            'profile.role' => EnsureProfileRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (AuthenticationException $exception, Request $request) {
            if ($request->is('api/*')) {
                return response()->json(['error' => 'Unauthenticated'], 401);
            }

            return null;
        });

        $exceptions->render(function (AuthorizationException $exception, Request $request) {
            if ($request->is('api/*')) {
                return response()->json(['error' => 'Forbidden'], 403);
            }

            return null;
        });

        $exceptions->render(function (NotFoundHttpException $exception, Request $request) {
            if ($request->is('api/*')) {
                return response()->json(['error' => 'Not found'], 404);
            }

            return null;
        });
    })->create();
