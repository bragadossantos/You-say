<?php

use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Session\TokenMismatchException;

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
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Quando o token CSRF não coincide (sessão expirada/cookie antigo),
        // regenera a sessão e redireciona de volta ao formulário com aviso.
        $exceptions->render(function (TokenMismatchException $e, $request) {
            $request->session()->regenerateToken();
            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->withErrors(['session' => 'A sua sessão expirou. Por favor tente novamente.']);
        });
    })->create();

