<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CleanupDownloadsCommand extends Command
{
    protected $signature = 'certificates:cleanup-downloads';

    protected $description = 'Cleanup old certificate download files (older than 1 hour)';

    public function handle()
    {
        $tempDir = storage_path('app/temp');

        if (!is_dir($tempDir)) {
            $this->info('No temp directory found. Nothing to clean.');
            return 0;
        }

        $files = glob($tempDir . '/*');
        $now = time();
        $maxAge = 3600; // 1 hour
        $deletedCount = 0;

        foreach ($files as $file) {
            if (is_file($file) && ($now - filemtime($file) > $maxAge)) {
                @unlink($file);
                $deletedCount++;
                $this->info("Deleted file: " . basename($file));
            } elseif (is_dir($file) && ($now - filemtime($file) > $maxAge)) {
                // Remove directory and its contents
                $dirFiles = glob($file . '/*');
                foreach ($dirFiles as $dirFile) {
                    @unlink($dirFile);
                }
                @rmdir($file);
                $deletedCount++;
                $this->info("Deleted directory: " . basename($file));
            }
        }

        $this->info("Cleanup completed. Deleted {$deletedCount} item(s).");
        return 0;
    }
}
