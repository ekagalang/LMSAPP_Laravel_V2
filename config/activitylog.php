<?php

return [
    // Whether to log GET requests. Default: false (only data-changing requests are logged)
    'log_get' => env('ACTIVITY_LOG_GET', false),

    // Route names allowed for GET logging when log_get = true
    // Add route names for downloads/exports or other important reads you want recorded
    'get_allowlist' => array_filter(array_map('trim', explode(',', env('ACTIVITY_LOG_GET_ALLOWLIST',
        implode(',', [
            'courses.progress.pdf',
            'courses.exportProgressPdf',
            'certificates.download',
            'certificates.public-download',
            'activity-logs.export',
        ])
    )))),
];

