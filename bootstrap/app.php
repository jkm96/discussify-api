<?php

use App\Http\Middleware\ConvertRequestFieldsToSnakeCase;
use App\Http\Middleware\ConvertResponseFieldsToCamelCase;
use App\Utils\Helpers\ResponseHelpers;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('api')
                ->prefix('api')
                ->name('admin.')
                ->group(base_path('routes/api/v1/user.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->appendToGroup('api', [
            ConvertResponseFieldsToCamelCase::class,
            ConvertRequestFieldsToSnakeCase::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (ModelNotFoundException $e) {
            $message = 'Entry for ' . str_replace('App\\', '', $e->getModel()) . ' not found';
            return ResponseHelpers::ConvertToJsonResponseWrapper([], $message, 404);
        });

        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            return ResponseHelpers::ConvertToJsonResponseWrapper([], "Page Not Found.", 404);
        });

    })->create();
