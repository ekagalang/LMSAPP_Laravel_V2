<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Reflection Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration options for the reflection system
    | optimized for production performance.
    |
    */

    'cache' => [
        /*
        |--------------------------------------------------------------------------
        | Analytics Cache Duration
        |--------------------------------------------------------------------------
        |
        | How long (in minutes) to cache analytics data. In production,
        | consider using longer cache times for better performance.
        |
        */
        'analytics_ttl' => env('REFLECTION_ANALYTICS_CACHE_TTL', 15),

        /*
        |--------------------------------------------------------------------------
        | Cache Tags
        |--------------------------------------------------------------------------
        |
        | Cache tags for easier cache invalidation in production.
        |
        */
        'tags' => [
            'analytics' => 'reflection_analytics',
            'user_data' => 'reflection_user_data',
        ],
    ],

    'pagination' => [
        /*
        |--------------------------------------------------------------------------
        | Default Pagination Size
        |--------------------------------------------------------------------------
        |
        | Default number of reflections to show per page.
        |
        */
        'per_page' => env('REFLECTION_PAGINATION_SIZE', 10),

        /*
        |--------------------------------------------------------------------------
        | Max Pagination Size
        |--------------------------------------------------------------------------
        |
        | Maximum number of reflections allowed per page to prevent
        | performance issues.
        |
        */
        'max_per_page' => env('REFLECTION_MAX_PAGINATION_SIZE', 50),
    ],

    'validation' => [
        /*
        |--------------------------------------------------------------------------
        | Content Limits
        |--------------------------------------------------------------------------
        |
        | Limits for reflection content to optimize database performance.
        |
        */
        'title_max_length' => 255,
        'content_max_length' => 5000,
        'content_min_length' => 10,
        'max_tags' => 10,
        'tag_max_length' => 50,
    ],

    'performance' => [
        /*
        |--------------------------------------------------------------------------
        | Query Optimization
        |--------------------------------------------------------------------------
        |
        | Settings for query optimization in production.
        |
        */
        'eager_load_relations' => true,
        'select_specific_columns' => true,
        'use_database_indexes' => true,

        /*
        |--------------------------------------------------------------------------
        | Cache Strategy
        |--------------------------------------------------------------------------
        |
        | Caching strategy for different data types.
        |
        */
        'cache_user_reflections' => env('CACHE_USER_REFLECTIONS', false),
        'cache_analytics' => env('CACHE_ANALYTICS', true),
        'cache_instructor_views' => env('CACHE_INSTRUCTOR_VIEWS', false),
    ],
];