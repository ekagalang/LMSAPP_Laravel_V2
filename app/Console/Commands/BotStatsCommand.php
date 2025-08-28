<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class BotStatsCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'bot:stats {--days=7 : Number of days to analyze}';

    /**
     * The console command description.
     */
    protected $description = 'Show bot detection statistics from logs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $this->info("📊 Analyzing bot detection stats for the last {$days} days...");
        $this->newLine();

        try {
            // Baca log file Laravel
            $logPath = storage_path('logs/laravel.log');
            
            if (!file_exists($logPath)) {
                $this->error('❌ Log file not found: ' . $logPath);
                return Command::FAILURE;
            }

            $logContent = file_get_contents($logPath);
            $lines = explode("\n", $logContent);
            
            // Statistik
            $honeypotLogin = 0;
            $honeypotRegister = 0;
            $suspiciousUA = 0;
            $emptyUA = 0;
            $totalBlocked = 0;
            $ipStats = [];
            $dateStats = [];
            
            // Filter berdasarkan tanggal
            $cutoffDate = now()->subDays($days);
            
            foreach ($lines as $line) {
                // Skip baris kosong
                if (empty(trim($line))) continue;
                
                // Extract tanggal dari log Laravel
                if (preg_match('/\[(\d{4}-\d{2}-\d{2})/', $line, $matches)) {
                    $logDate = $matches[1];
                    if ($logDate < $cutoffDate->format('Y-m-d')) {
                        continue;
                    }
                }
                
                // Hitung berbagai jenis deteksi bot
                if (strpos($line, '🍯 Honeypot triggered pada login') !== false) {
                    $honeypotLogin++;
                    $totalBlocked++;
                    $this->extractIPFromLog($line, $ipStats);
                    $this->extractDateFromLog($line, $dateStats);
                }
                
                if (strpos($line, '🍯 Honeypot triggered pada register') !== false) {
                    $honeypotRegister++;
                    $totalBlocked++;
                    $this->extractIPFromLog($line, $ipStats);
                    $this->extractDateFromLog($line, $dateStats);
                }
                
                if (strpos($line, '🤖 Suspicious user agent detected') !== false) {
                    $suspiciousUA++;
                    $totalBlocked++;
                    $this->extractIPFromLog($line, $ipStats);
                    $this->extractDateFromLog($line, $dateStats);
                }
                
                if (strpos($line, '🚫 Empty atau short user agent') !== false) {
                    $emptyUA++;
                    $totalBlocked++;
                    $this->extractIPFromLog($line, $ipStats);
                    $this->extractDateFromLog($line, $dateStats);
                }
            }
            
            // Tampilkan hasil
            $this->displayStats($honeypotLogin, $honeypotRegister, $suspiciousUA, $emptyUA, $totalBlocked);
            $this->displayTopOffenders($ipStats);
            $this->displayDailyStats($dateStats);
            
        } catch (\Exception $e) {
            $this->error('❌ Error reading log file: ' . $e->getMessage());
            return Command::FAILURE;
        }
        
        return Command::SUCCESS;
    }
    
    /**
     * Display main statistics
     */
    private function displayStats($honeypotLogin, $honeypotRegister, $suspiciousUA, $emptyUA, $total)
    {
        $this->info('🛡️  BOT DETECTION SUMMARY');
        $this->table(
            ['Detection Type', 'Count', 'Percentage'],
            [
                ['🍯 Honeypot (Login)', $honeypotLogin, $total > 0 ? round(($honeypotLogin/$total)*100, 1).'%' : '0%'],
                ['🍯 Honeypot (Register)', $honeypotRegister, $total > 0 ? round(($honeypotRegister/$total)*100, 1).'%' : '0%'],
                ['🤖 Suspicious User Agent', $suspiciousUA, $total > 0 ? round(($suspiciousUA/$total)*100, 1).'%' : '0%'],
                ['🚫 Empty/Short User Agent', $emptyUA, $total > 0 ? round(($emptyUA/$total)*100, 1).'%' : '0%'],
                ['', '', ''],
                ['🎯 TOTAL BLOCKED', $total, '100%'],
            ]
        );
        $this->newLine();
    }
    
    /**
     * Display top IP offenders
     */
    private function displayTopOffenders($ipStats)
    {
        if (empty($ipStats)) {
            $this->info('✅ No suspicious IPs detected in this period.');
            $this->newLine();
            return;
        }
        
        // Sort by count descending
        arsort($ipStats);
        $topIPs = array_slice($ipStats, 0, 10, true);
        
        $this->info('🚨 TOP IP OFFENDERS');
        $tableData = [];
        foreach ($topIPs as $ip => $count) {
            $tableData[] = [$ip, $count];
        }
        
        $this->table(['IP Address', 'Attempts'], $tableData);
        $this->newLine();
    }
    
    /**
     * Display daily statistics
     */
    private function displayDailyStats($dateStats)
    {
        if (empty($dateStats)) {
            $this->info('📅 No bot activity detected in the specified period.');
            $this->newLine();
            return;
        }
        
        ksort($dateStats);
        
        $this->info('📅 DAILY BREAKDOWN');
        $tableData = [];
        foreach ($dateStats as $date => $count) {
            $tableData[] = [$date, $count];
        }
        
        $this->table(['Date', 'Bot Attempts'], $tableData);
        $this->newLine();
        
        // Recommendations
        $this->displayRecommendations($dateStats);
    }
    
    /**
     * Extract IP address from log line
     */
    private function extractIPFromLog($line, &$ipStats)
    {
        if (preg_match('/"ip":"([^"]+)"/', $line, $matches)) {
            $ip = $matches[1];
            $ipStats[$ip] = ($ipStats[$ip] ?? 0) + 1;
        }
    }
    
    /**
     * Extract date from log line
     */
    private function extractDateFromLog($line, &$dateStats)
    {
        if (preg_match('/\[(\d{4}-\d{2}-\d{2})/', $line, $matches)) {
            $date = $matches[1];
            $dateStats[$date] = ($dateStats[$date] ?? 0) + 1;
        }
    }
    
    /**
     * Display recommendations based on stats
     */
    private function displayRecommendations($dateStats)
    {
        $avgDaily = count($dateStats) > 0 ? array_sum($dateStats) / count($dateStats) : 0;
        $maxDaily = count($dateStats) > 0 ? max($dateStats) : 0;
        
        $this->info('💡 RECOMMENDATIONS');
        
        if ($maxDaily > 100) {
            $this->warn('⚠️  High bot activity detected! Consider implementing additional security measures.');
        }
        
        if ($avgDaily > 50) {
            $this->warn('⚠️  Consider implementing IP-based blocking for repeat offenders.');
        }
        
        if ($avgDaily > 0) {
            $this->info('✅ Honeypot is working effectively! Keep monitoring regularly.');
        } else {
            $this->comment('ℹ️  No bot activity detected in the specified period.');
        }
        
        $this->newLine();
        $this->info('📈 Average daily bot attempts: ' . round($avgDaily, 1));
        $this->info('🔥 Peak daily bot attempts: ' . $maxDaily);
        $this->newLine();
    }
}