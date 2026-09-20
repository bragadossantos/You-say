<?php

return [
    'default' => env('FILESYSTEM_DISK', 'public'),
    'disks' => [
        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => true,
            'throw' => false,
        ],
        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw' => false,
        ],

        // Bucket S3-compativel (Cloudflare R2, Supabase Storage ou AWS S3) usado
        // para guardar PDFs de monografias de forma persistente na Vercel. Sem
        // estas variaveis definidas, App\Models\Article::documentDisk() cai
        // automaticamente para o disco 'public' acima.
        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION', 'auto'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'visibility' => 'private',
            'throw' => false,
        ],
    ],
    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],
];
