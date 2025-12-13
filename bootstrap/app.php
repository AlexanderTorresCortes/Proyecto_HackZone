<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Registrar el middleware de rol
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Manejar errores específicos primero (más específicos primero)
        
        // 404 - Página no encontrada
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Recurso no encontrado'], 404);
            }
            return response()->view('errors.404', [], 404);
        });

        // 403 - Acceso prohibido
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Acceso denegado'], 403);
            }
            return response()->view('errors.403', [], 403);
        });

        // 419 - Token de sesión expirado
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Token de sesión expirado'], 419);
            }
            return response()->view('errors.419', [], 419);
        });

        // Otros errores HTTP (503, 429, etc.)
        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\HttpException $e, $request) {
            // Evitar capturar NotFoundHttpException y AccessDeniedHttpException que ya fueron manejados
            if ($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException ||
                $e instanceof \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException) {
                return null;
            }

            $statusCode = $e->getStatusCode();
            
            if ($request->expectsJson()) {
                return response()->json(['message' => $e->getMessage() ?: 'Error HTTP'], $statusCode);
            }

            // Manejar diferentes códigos de error HTTP con vistas personalizadas
            if (view()->exists("errors.{$statusCode}")) {
                return response()->view("errors.{$statusCode}", [], $statusCode);
            }
            
            // Si no hay vista específica, usar la vista 500
            return response()->view('errors.500', [], $statusCode);
        });

        // Manejar errores generales del servidor (último recurso)
        $exceptions->render(function (\Throwable $e, $request) {
            // Loggear el error siempre
            \Log::error('=== ERROR 500 NO CAPTURADO ===');
            \Log::error('Tipo: ' . get_class($e));
            \Log::error('Mensaje: ' . $e->getMessage());
            \Log::error('Archivo: ' . $e->getFile());
            \Log::error('Línea: ' . $e->getLine());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            \Log::error('================================');
            
            // En modo debug, mostrar el error detallado de Laravel
            if (config('app.debug')) {
                return null;
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Error interno del servidor',
                    'error' => $e->getMessage()
                ], 500);
            }

            return response()->view('errors.500', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        });
    })->create();