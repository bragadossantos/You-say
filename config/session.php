<?php

return [
    'driver' => env('SESSION_DRIVER', 'cookie'),
    // env('SESSION_LIFETIME', 120) só usa o valor por omissão quando a
    // variável NÃO está definida — se estiver definida mas vazia (ex.:
    // configurada em branco no painel da Vercel), (int) transforma-a em 0,
    // o que faz o navegador expirar a cookie de sessão IMEDIATAMENTE
    // (Max-Age=0) e o login nunca fica guardado. O `?:` protege contra isso.
    'lifetime' => (int) (env('SESSION_LIFETIME') ?: 120),
    'expire_on_close' => false,
    'encrypt' => env('SESSION_ENCRYPT', false),
    'files' => storage_path('framework/sessions'),
    'connection' => env('SESSION_CONNECTION'),
    'table' => 'sessions',
    'store' => env('SESSION_STORE'),
    'lottery' => [2, 100],
    'cookie' => env('SESSION_COOKIE', 'artigos_session'),
    'path' => '/',
    'domain' => env('SESSION_DOMAIN'),
    'secure' => env('SESSION_SECURE_COOKIE', false),
    'http_only' => true,
    'same_site' => 'lax',
    'partitioned' => false,
];
