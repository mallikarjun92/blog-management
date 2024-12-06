<?php

return [
    'db' => [
        'host' => 'localhost',
        'dbname' => 'blog_management',
        'user' => 'root',
        'password' => '1234',
        'charset' => 'utf8mb4',
    ],
    'app' => [
        'base_url' => 'http://localhost:8000', // Base URL of your app
        'debug' => true, // Enable/disable debug mode
    ],
    'security' => [
        'csrf_token_key' => 'csrf_token', // CSRF token key for forms
    ],
];
