<?php

use Illuminate\Support\Str;

return [
    'stack' => 'inertia',
    'middleware' => ['web', 'admin'],
    'driver' => env('SESSION_DRIVER', 'file'),

    'models' => [
        'session' => env('SESSION_MODEL', 'App\Models\Session'),
        'guards' => [
            'admin' => env('SESSION_GUARD_MODEL', 'App\Models\Admin'),
            'user' => env('SESSION_GUARD_MODEL', 'App\Models\User'),
        ],
    ],
];
