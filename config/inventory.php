<?php

declare(strict_types=1);

return [
    'initial_admin' => [
        'name' => env('INITIAL_ADMIN_NAME', 'System Administrator'),
        'email' => env('INITIAL_ADMIN_EMAIL'),
        'password' => env('INITIAL_ADMIN_PASSWORD'),
    ],
];
