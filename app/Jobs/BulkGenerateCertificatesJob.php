<?php

namespace App\Jobs;

use App\Http\Controllers\CertificateController;
use App\Models\Course;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class BulkGenerateCertificatesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 600; // 10 minutes
    public $tries = 3;

    protected $course;
    protected $userIds;
    protected $batchId;

    /**
     * Create a new job instance.
     */
    public function __construct(Course $course, array $userIds, string $batchId = null)
    {
        $this->course = $course;
        $this->userIds = $userIds;
        $this->batchId = $batchId ?? uniqid('certbatch_', true);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Bulk Certificate Generation Job Started", [
            'batch_id' => $this->batchId,
            'course_id' => $this->course->id,
            'user_count' => count($this->userIds)
        ]);

        $generated = 0;
        $skipped = 0;
        $errors = 0;

        foreach ($this->userIds as $userId) {
            try {
                $user = User::find($userId);
                if (!$user) {
                    Log::warning("User not found for certificate generation", [
                        'user_id' => $userId,
                        'batch_id' => $this->batchId
                    ]);
                    $skipped++;
                    continue;
                }

                $certificate = CertificateController::generateForUser($this->course, $user);

                if ($certificate) {
                    $generated++;
                    Log::info("Certificate generated in batch", [
                        'user_id' => $user->id,
                        'certificate_id' => $certificate->id,
                        'batch_id' => $this->batchId
                    ]);
                } else {
                    $skipped++;
                    Log::info("Certificate skipped (may already exist)", [
                        'user_id' => $user->id,
                        'batch_id' => $this->batchId
                    ]);
                }

                // Small delay to prevent overwhelming the system
                if ($generated % 10 === 0) {
                    usleep(100000); // 100ms delay every 10 certificates
                }

            } catch (\Exception $e) {
                $errors++;
                Log::error("Certificate generation failed in batch", [
                    'user_id' => $userId,
                    'course_id' => $this->course->id,
                    'error' => $e->getMessage(),
                    'batch_id' => $this->batchId
                ]);
            }
        }

        Log::info("Bulk Certificate Generation Job Completed", [
            'batch_id' => $this->batchId,
            'generated' => $generated,
            'skipped' => $skipped,
            'errors' => $errors,
            'total' => count($this->userIds)
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Bulk Certificate Generation Job Failed", [
            'batch_id' => $this->batchId,
            'course_id' => $this->course->id,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}
