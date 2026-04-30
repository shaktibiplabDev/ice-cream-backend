<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => [
        'https://demo-celesty.versaero.top',  // Production frontend
        'http://localhost:3000',               // React dev server
        'http://localhost:5173',               // Vite dev server
        'http://localhost:8000',               // Laravel dev server
        'http://localhost:8080',               // Common dev port
        'http://127.0.0.1:3000',
        'http://127.0.0.1:5173',
        'http://127.0.0.1:8000',
    ],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];