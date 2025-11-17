<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use App\Models\User;

class ClearDashboardCacheCommand extends Command
{
    protected $signature = 'dashboard:clear-cache {--user_id= : Clear cache for specific user ID} {--role= : Clear cache for all users with specific role}';

    protected $description = 'Clear dashboard cache for EO, Instructor, or all users';

    public function handle()
    {
        $userId = $this->option('user_id');
        $role = $this->option('role');

        if ($userId) {
            // Clear cache for specific user
            $this->clearCacheForUser($userId);
            $this->info("Dashboard cache cleared for user ID: {$userId}");
        } elseif ($role) {
            // Clear cache for all users with specific role
            $users = User::role($role)->get();
            $count = 0;
            foreach ($users as $user) {
                $this->clearCacheForUser($user->id);
                $count++;
            }
            $this->info("Dashboard cache cleared for {$count} users with role: {$role}");
        } else {
            // Clear all dashboard caches
            $patterns = [
                'eo_stats_*',
                'instructor_stats_*',
            ];

            foreach ($patterns as $pattern) {
                // Note: This requires cache driver that supports pattern deletion
                // For file/redis cache, you may need to clear all cache
                $this->info("Attempting to clear cache pattern: {$pattern}");
            }

            // Fallback: clear entire cache
            Cache::flush();
            $this->info('All cache cleared successfully');
        }

        return 0;
    }

    private function clearCacheForUser($userId)
    {
        Cache::forget('eo_stats_' . $userId);
        Cache::forget('instructor_stats_' . $userId);
    }
}
