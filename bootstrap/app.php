<?php

use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\PreventBackHistoryCache;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: '*');
        $middleware->validateCsrfTokens(except: [
            // 'api/*',
        ]);
        $middleware->alias([
            'admin' => EnsureUserIsAdmin::class,
            'nocache' => PreventBackHistoryCache::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Quando o token CSRF não coincide (sessão expirada/cookie antigo),
        // regenera a sessão e redireciona de volta ao formulário com aviso.
        //
        // NOTA: no Laravel 11 o TokenMismatchException é convertido em
        // HttpException(419) por Handler::prepareException() ANTES de os
        // callbacks "render" serem avaliados — por isso é preciso apanhar
        // pelo código de estado (419) em vez do tipo da exceção original,
        // senão este callback nunca é executado.
        $exceptions->render(function (HttpException $e, $request) {
            if ($e->getStatusCode() === 419 && ! $request->expectsJson()) {
                $request->session()->regenerateToken();

                return back()
                    ->withInput($request->except('password', 'password_confirmation'))
                    ->withErrors(['session' => 'A sua sessão expirou. Por favor tente novamente.']);
            }
        });
    })->create();

