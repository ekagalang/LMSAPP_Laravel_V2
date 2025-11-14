<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule cleanup command to run every hour
Schedule::command('certificates:cleanup-downloads')->hourly();

// Audit permission middleware coverage across named routes
Artisan::command('permissions:audit {--format=table : Output format: table|csv|json} {--missing-only : Show only routes without permission middleware} {--name= : Filter by route name contains} {--method= : Filter by HTTP method} {--path= : Filter by URI contains}', function () {
    $routes = collect(Route::getRoutes());

    $filterName = strtolower((string) $this->option('name'));
    $filterMethod = strtoupper((string) $this->option('method'));
    $filterPath = strtolower((string) $this->option('path'));
    $missingOnly = (bool) $this->option('missing-only');
    $format = strtolower((string) $this->option('format')) ?: 'table';

    $rows = [];

    foreach ($routes as $route) {
        $name = $route->getName();
        // Focus on named routes to keep output relevant
        if (!$name) continue;

        $uri = $route->uri();
        $methods = implode('|', array_diff($route->methods(), ['HEAD']));
        $middleware = $route->gatherMiddleware();

        $perms = collect($middleware)
            ->filter(function ($m) {
                if (!is_string($m)) return false;
                if (str_starts_with($m, 'permission:')) return true;
                $fqcn = \Spatie\Permission\Middlewares\PermissionMiddleware::class . ':';
                return str_starts_with($m, $fqcn);
            })
            ->values()
            ->all();

        $protected = !empty($perms);

        // Filters
        if ($filterName && !str_contains(strtolower($name), $filterName)) continue;
        if ($filterMethod && $filterMethod !== '' && !str_contains($methods, $filterMethod)) continue;
        if ($filterPath && !str_contains(strtolower($uri), $filterPath)) continue;
        if ($missingOnly && $protected) continue;

        $rows[] = [
            'name' => $name,
            'methods' => $methods,
            'uri' => $uri,
            'protected' => $protected ? 'yes' : 'no',
            'permission_middleware' => implode(',', $perms),
        ];
    }

    // Sort by name for stable output
    usort($rows, fn($a, $b) => strcmp($a['name'], $b['name']));

    if ($format === 'json') {
        $this->line(json_encode($rows, JSON_PRETTY_PRINT));
        return 0;
    }

    if ($format === 'csv') {
        $out = fopen('php://output', 'w');
        fputcsv($out, ['name', 'methods', 'uri', 'protected', 'permission_middleware']);
        foreach ($rows as $r) fputcsv($out, $r);
        fclose($out);
        return 0;
    }

    $this->table(['name', 'methods', 'uri', 'protected', 'permission_middleware'], $rows);
    $this->info('Total: ' . count($rows));
    if ($missingOnly) {
        $this->warn('Showing only routes without permission middleware.');
    }
    return 0;
})->purpose('Audit permission middleware coverage for routes');
