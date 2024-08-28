<?php

use Illuminate\Support\Str;

return [
    'stack' => 'inertia',
    'middleware' => ['web', 'admin'],
    'models' => [
        'session' => env('SESSION_MODEL', 'App\Models\Session'),
        'guards' => [
            'admin' => env('SESSION_GUARD_MODEL', 'App\Models\Admin'),
            'user' => env('SESSION_GUARD_MODEL', 'App\Models\User'),
        ],
    ],
];
