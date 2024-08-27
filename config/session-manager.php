<?php

use Illuminate\Support\Str;

return [
    'driver' => env('SESSION_DRIVER', 'file'),

    'models' => [
        'session' => env('SESSION_MODEL', 'App\Models\Session'),
        'guards' => [
            'user' => env('SESSION_GUARD_MODEL', 'App\Models\Users'),
        ],
    ],
];
