<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class BotCleanCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'bot:clean {--days=30 : Keep logs for this many days} {--dry-run : Show what would be deleted without actually deleting}';

    /**
     * The console command description.
     */
    protected $description = 'Clean old bot detection logs to keep log files manageable';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $dryRun = $this->option('dry-run');
        
        $this->info("🧹 Cleaning bot logs older than {$days} days...");
        $this->newLine();

        try {
            $logPath = storage_path('logs/laravel.log');
            
            if (!file_exists($logPath)) {
                $this->error('❌ Log file not found: ' . $logPath);
                return Command::FAILURE;
            }

            $logContent = file_get_contents($logPath);
            $lines = explode("\n", $logContent);
            
            $cutoffDate = now()->subDays($days);
            $botLogLines = [];
            $cleanedLines = [];
            $botLogCount = 0;
            
            foreach ($lines as $line) {
                $isBotLog = false;
                
                // Check if this is a bot-related log entry
                if (strpos($line, '🍯 Honeypot triggered') !== false ||
                    strpos($line, '🤖 Suspicious user agent') !== false ||
                    strpos($line, '🚫 Empty atau short user agent') !== false) {
                    
                    // Extract date from log line
                    if (preg_match('/\[(\d{4}-\d{2}-\d{2})/', $line, $matches)) {
                        $logDate = $matches[1];
                        if ($logDate < $cutoffDate->format('Y-m-d')) {
                            $isBotLog = true;
                            $botLogCount++;
                            $botLogLines[] = $line;
                        }
                    }
                }
                
                // Keep non-bot logs or recent bot logs
                if (!$isBotLog) {
                    $cleanedLines[] = $line;
                }
            }
            
            if ($botLogCount === 0) {
                $this->info('✅ No old bot logs found to clean.');
                return Command::SUCCESS;
            }
            
            if ($dryRun) {
                $this->warn("🔍 DRY RUN - Would delete {$botLogCount} old bot log entries:");
                $this->newLine();
                
                // Show sample of what would be deleted
                $sampleLines = array_slice($botLogLines, 0, 5);
                foreach ($sampleLines as $line) {
                    $this->line('  ' . substr($line, 0, 100) . '...');
                }
                
                if (count($botLogLines) > 5) {
                    $this->line('  ... and ' . (count($botLogLines) - 5) . ' more lines');
                }
                
                $this->newLine();
                $this->info('Run without --dry-run to actually clean the logs.');
            } else {
                // Actually clean the logs
                $newLogContent = implode("\n", $cleanedLines);
                
                // Backup original log file
                $backupPath = storage_path('logs/laravel.log.backup.' . now()->format('Y-m-d-H-i-s'));
                copy($logPath, $backupPath);
                
                // Write cleaned content
                file_put_contents($logPath, $newLogContent);
                
                $this->info("✅ Cleaned {$botLogCount} old bot log entries.");
                $this->info("📦 Backup created at: {$backupPath}");
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Error cleaning log file: ' . $e->getMessage());
            return Command::FAILURE;
        }
        
        return Command::SUCCESS;
    }
}