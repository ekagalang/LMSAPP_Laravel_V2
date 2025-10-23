<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use Closure;
use Illuminate\Http\Request;

class LogActivity
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Pre-capture for before/after diffs on bound models (for update/delete)
        $preModels = [];
        if (auth()->check()) {
            $method = strtoupper($request->getMethod());
            if (in_array($method, ['PUT','PATCH','DELETE'])) {
                $route = $request->route();
                if ($route) {
                    foreach ((array) $route->parameters() as $param) {
                        if ($param instanceof \Illuminate\Database\Eloquent\Model) {
                            $preModels[] = [
                                'class' => get_class($param),
                                'id' => $param->getKey(),
                                'before' => $param->getAttributes(),
                            ];
                        }
                    }
                }
            }
        }

        $response = $next($request);

        // Only log for authenticated users
        if (auth()->check()) {
            $method = strtoupper($request->getMethod());
            $route = $request->route();
            $routeName = $route?->getName() ?? '';

            // Data-changing requests are always logged
            if (!in_array($method, ['POST','PUT','PATCH','DELETE'])) {
                // For GET, only log if enabled and the route is allowlisted
                $logGet = (bool) config('activitylog.log_get', false);
                $allow = (array) config('activitylog.get_allowlist', []);
                if (!($logGet && $routeName && in_array($routeName, $allow))) {
                    return $response;
                }
            }
            $path = ltrim($request->path(), '/');

            // Skip logging for pure participants is handled inside ActivityLog::log

            $action = $route?->getActionName() ?? '';

            // Filter sensitive inputs
            $input = collect($request->all())->except([
                '_token', 'password', 'password_confirmation', 'current_password', 'new_password', 'new_password_confirmation'
            ])->toArray();

            // Build model diffs if any
            $models = [];
            if (!empty($preModels)) {
                foreach ($preModels as $m) {
                    $cls = $m['class'];
                    $id = $m['id'];
                    $before = $m['before'];
                    $after = null;
                    $state = 'deleted';
                    try {
                        $fresh = (new $cls)->find($id);
                        if ($fresh) {
                            $after = $fresh->getAttributes();
                            $state = 'updated';
                        }
                    } catch (\Throwable $e) {}

                    // Compute changed fields
                    $changed = [];
                    if ($after !== null) {
                        foreach (array_unique(array_merge(array_keys($before), array_keys($after))) as $key) {
                            if (in_array($key, ['updated_at','created_at','remember_token','password'])) continue;
                            $bv = $before[$key] ?? null;
                            $av = $after[$key] ?? null;
                            if ($bv !== $av) $changed[] = $key;
                        }
                    }

                    $models[] = [
                        'model' => $cls,
                        'id' => $id,
                        'state' => $state,
                        'changed_fields' => $changed,
                        'before' => $before,
                        'after' => $after,
                    ];
                }
            }

            $action = 'http_' . strtolower($request->method());

            ActivityLog::log($action, [
                'description' => sprintf('%s %s', $request->method(), $request->path()),
                'metadata' => [
                    'method' => $request->method(),
                    'path' => $request->path(),
                    'route' => $routeName,
                    'action' => $action,
                    'status' => $response->getStatusCode(),
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'input' => $input,
                    'models' => $models,
                ],
            ]);
        }

        return $response;
    }
}
