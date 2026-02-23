<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Evita Route [login] not defined: para rutas API no redirigir, devolver 401 JSON
        $middleware->redirectGuestsTo(fn (Request $request) =>
            $request->is('api/*') ? null : (Route::has('login') ? route('login') : '/')
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\DomainException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => $e->getMessage(),
                    'error' => 'domain_error',
                    'details' => ['context' => class_basename($e)],
                ], 422);
            }
        });

        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'No autenticado. Use POST /api/auth/login o POST /api/auth/register para obtener un token.',
                    'error' => 'unauthenticated',
                    'hint' => 'Incluya el header: Authorization: Bearer {token}',
                ], 401);
            }
        });

        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Error de validación.',
                    'error' => 'validation_error',
                    'errors' => $e->errors(),
                ], 422);
            }
        });

        $exceptions->render(function (NotFoundHttpException|ModelNotFoundException $e, Request $request) {
            if ($request->is('api/*')) {
                $message = $e instanceof ModelNotFoundException
                    ? 'Recurso no encontrado.'
                    : 'Ruta o recurso no encontrado.';
                return response()->json([
                    'message' => $message,
                    'error' => 'not_found',
                ], 404);
            }
        });

        $exceptions->render(function (HttpException $e, Request $request) {
            if ($request->is('api/*') && ! $e instanceof NotFoundHttpException) {
                return response()->json([
                    'message' => $e->getMessage() ?: 'Error en la solicitud.',
                    'error' => 'http_error',
                    'status' => $e->getStatusCode(),
                ], $e->getStatusCode());
            }
        });

        $exceptions->render(function (Throwable $e, Request $request) {
            if ($request->is('api/*')) {
                $isDebug = config('app.debug');
                $payload = [
                    'message' => $isDebug ? $e->getMessage() : 'Error interno del servidor.',
                    'error' => 'server_error',
                ];
                if ($isDebug) {
                    $payload['exception'] = class_basename($e);
                    $payload['file'] = $e->getFile();
                    $payload['line'] = $e->getLine();
                }
                return response()->json($payload, 500);
            }
        });
    })->create();
