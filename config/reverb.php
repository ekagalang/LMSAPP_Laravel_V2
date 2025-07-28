<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Reverb Server
    |--------------------------------------------------------------------------
    |
    | This option controls the default server used by Reverb to handle
    | WebSocket connections. This server is used when no server is
    | explicitly specified when starting the Reverb server.
    |
    */

    'default' => env('REVERB_SERVER', 'reverb'),

    /*
    |--------------------------------------------------------------------------
    | Reverb Servers
    |--------------------------------------------------------------------------
    |
    | Here you may define details for each of the Reverb servers that
    | are used by your application. Each server has a host and port
    | combination along with additional Pusher configuration.
    |
    */

    'servers' => [

        'reverb' => [
            'host' => env('REVERB_HOST', '0.0.0.0'),
            'port' => env('REVERB_PORT', 8080),
            'hostname' => env('REVERB_HOSTNAME', '127.0.0.1'),
            'options' => [
                'tls' => [],
            ],
            'max_request_size' => env('REVERB_MAX_REQUEST_SIZE', 10_000),
            'scaling' => [
                'enabled' => env('REVERB_SCALING_ENABLED', false),
                'channel' => env('REVERB_SCALING_CHANNEL', 'reverb'),
                'server' => [
                    'url' => env('REDIS_URL'),
                    'host' => env('REDIS_HOST', '127.0.0.1'),
                    'port' => env('REDIS_PORT', 6379),
                    'username' => env('REDIS_USERNAME'),
                    'password' => env('REDIS_PASSWORD'),
                    'database' => env('REDIS_DB', 0),
                ],
            ],
            'pulse' => [
                'enabled' => env('REVERB_PULSE_ENABLED', false),
                'interval' => env('REVERB_PULSE_INTERVAL', 30),
                'ingest' => [
                    'enabled' => env('REVERB_PULSE_INGEST_ENABLED', false),
                    'trim' => [
                        'lottery' => [1, 1_000],
                        'keep' => '7 days',
                    ],
                ],
            ],
            'pulse_ingest_interval' => env('REVERB_PULSE_INGEST_INTERVAL', 15),
            'telescope_ingest_interval' => env('REVERB_TELESCOPE_INGEST_INTERVAL', 15),
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Reverb Applications
    |--------------------------------------------------------------------------
    |
    | Here you may define how Reverb applications are managed. A default
    | provider is configured to use the local application credentials
    | from your broadcasting configuration.
    |
    */

    'apps' => [

        'provider' => 'config',

        'apps' => [
            [
                'app_id' => env('REVERB_APP_ID'),
                'app_key' => env('REVERB_APP_KEY'),
                'app_secret' => env('REVERB_APP_SECRET'),
                'options' => [
                    'host' => env('REVERB_HOST'),
                    'port' => env('REVERB_PORT', 443),
                    'scheme' => env('REVERB_SCHEME', 'https'),
                    'useTLS' => env('REVERB_SCHEME', 'https') === 'https',
                ],
                'allowed_origins' => ['*'],
                'ping_interval' => env('REVERB_PING_INTERVAL', 30),
                'activity_timeout' => env('REVERB_ACTIVITY_TIMEOUT', 120),
                'max_message_size' => env('REVERB_MAX_MESSAGE_SIZE', 10_000),
            ],
        ],

    ],

];