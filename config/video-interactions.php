<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Video Interactions Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for video interaction system
    |
    */

    'cache' => [
        'enabled' => env('VIDEO_INTERACTIONS_CACHE_ENABLED', true),
        'ttl' => env('VIDEO_INTERACTIONS_CACHE_TTL', 300), // 5 minutes
        'prefix' => env('VIDEO_INTERACTIONS_CACHE_PREFIX', 'video_interactions_'),
    ],

    'performance' => [
        'time_tracking_interval' => env('VIDEO_INTERACTIONS_TIME_INTERVAL', 250), // milliseconds
        'interaction_tolerance' => env('VIDEO_INTERACTIONS_TOLERANCE', 0.5), // seconds
        'api_timeout' => env('VIDEO_INTERACTIONS_API_TIMEOUT', 10), // seconds
    ],

    'rate_limiting' => [
        'content_requests' => env('VIDEO_INTERACTIONS_RATE_CONTENT', 60), // per minute
        'response_submissions' => env('VIDEO_INTERACTIONS_RATE_RESPONSES', 30), // per minute
    ],

    'logging' => [
        'enabled' => env('VIDEO_INTERACTIONS_LOG_ENABLED', false),
        'level' => env('VIDEO_INTERACTIONS_LOG_LEVEL', 'error'),
    ],
];