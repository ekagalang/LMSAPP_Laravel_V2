<?php

return [
    // Directories where Blade will look for views
    'paths' => [
        resource_path('views'),
    ],

    // Where compiled Blade templates are stored
    'compiled' => env('VIEW_COMPILED_PATH', realpath(storage_path('framework/views')) ?: storage_path('framework/views')),
];

