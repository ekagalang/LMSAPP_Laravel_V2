<?php

namespace App\Jobs;

use App\Models\Certificate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use ZipArchive;

class BulkDownloadCertificatesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 1800; // 30 minutes
    public $tries = 1;

    protected $batchId;
    protected $courseId;
    protected $search;

    public function __construct(string $batchId, $courseId = null, $search = null)
    {
        $this->batchId = $batchId;
        $this->courseId = $courseId;
        $this->search = $search;
    }

    public function handle(): void
    {
        Log::info("Bulk Download Job Started", [
            'batch_id' => $this->batchId,
            'course_id' => $this->courseId,
            'search' => $this->search
        ]);

        try {
            // Create status file
            $this->updateStatus('processing', 0);

            // Query certificates with filters
            $query = Certificate::with(['user', 'course']);

            if ($this->courseId) {
                $query->where('course_id', $this->courseId);
            }

            if ($this->search) {
                $query->whereHas('user', function ($q) {
                    $q->where('name', 'like', "%{$this->search}%");
                });
            }

            // Get all certificate IDs (efficient, only IDs)
            $certificateIds = $query->pluck('id')->toArray();
            $totalCount = count($certificateIds);

            Log::info("Found {$totalCount} certificates to download", [
                'batch_id' => $this->batchId
            ]);

            if ($totalCount === 0) {
                $this->updateStatus('failed', 0, 'Tidak ada sertifikat yang ditemukan');
                return;
            }

            // Create temporary directory for ZIP
            $tempDir = storage_path('app/temp/bulk_download_' . $this->batchId);
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            $zipFileName = 'certificates_' . now()->format('Y-m-d_His') . '.zip';
            $zipFilePath = $tempDir . '/' . $zipFileName;

            $zip = new ZipArchive();
            if ($zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                throw new \Exception('Could not create ZIP file');
            }

            $addedFiles = 0;
            $batchSize = 50; // Process 50 certificates at a time

            // Process in batches to avoid memory issues
            foreach (array_chunk($certificateIds, $batchSize) as $chunk) {
                $certificates = Certificate::whereIn('id', $chunk)
                    ->with(['user', 'course'])
                    ->get();

                foreach ($certificates as $certificate) {
                    if ($certificate->fileExists()) {
                        try {
                            $pdfPath = Storage::disk('public')->path($certificate->path);

                            if (file_exists($pdfPath)) {
                                $fileName = $this->generateUniqueFileName($certificate);

                                if ($zip->addFile($pdfPath, $fileName)) {
                                    $addedFiles++;
                                }
                            }
                        } catch (\Exception $e) {
                            Log::warning("Failed to add certificate to ZIP", [
                                'certificate_id' => $certificate->id,
                                'error' => $e->getMessage()
                            ]);
                        }
                    }

                    // Update status periodically
                    if ($addedFiles % 10 === 0) {
                        $this->updateStatus('processing', $addedFiles);
                    }
                }

                // Small delay to prevent overwhelming the system
                usleep(100000); // 100ms
            }

            $zip->close();

            if ($addedFiles === 0) {
                @unlink($zipFilePath);
                @rmdir($tempDir);
                $this->updateStatus('failed', 0, 'Tidak ada file sertifikat yang valid');
                return;
            }

            // Save ZIP file path for download
            $this->updateStatus('completed', $addedFiles, null, $zipFilePath);

            Log::info("Bulk Download Job Completed", [
                'batch_id' => $this->batchId,
                'added_files' => $addedFiles,
                'total_count' => $totalCount,
                'zip_path' => $zipFilePath
            ]);

        } catch (\Exception $e) {
            Log::error("Bulk Download Job Failed", [
                'batch_id' => $this->batchId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->updateStatus('failed', 0, $e->getMessage());
        }
    }

    private function generateUniqueFileName(Certificate $certificate): string
    {
        $courseName = Str::slug($certificate->course->title);
        $userName = Str::slug($certificate->user->name);
        $code = $certificate->certificate_code;

        return "Sertifikat-{$courseName}-{$userName}-{$code}.pdf";
    }

    private function updateStatus(string $status, int $processed, ?string $message = null, ?string $zipPath = null): void
    {
        $statusFile = storage_path('app/temp/download_status_' . $this->batchId . '.json');

        $data = [
            'status' => $status,
            'processed' => $processed,
            'updated_at' => now()->toIso8601String(),
        ];

        if ($message) {
            $data['message'] = $message;
        }

        if ($zipPath) {
            $data['zip_path'] = $zipPath;
        }

        file_put_contents($statusFile, json_encode($data));
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("Bulk Download Job Failed", [
            'batch_id' => $this->batchId,
            'error' => $exception->getMessage()
        ]);
        $this->updateStatus('failed', 0, $exception->getMessage());
    }
}
