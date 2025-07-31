<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;

class CertificateController extends Controller
{
    /**
     * Static method untuk generate certificate - dipanggil dari controller lain
     * TAMBAHAN: Method yang hilang ini yang menyebabkan error
     */
    public static function generateForUser(Course $course, User $user)
    {
        try {
            // Log untuk debugging
            Log::info("Attempting to generate certificate for user {$user->id} in course {$course->id}");

            // Cek apakah course punya template
            if (!$course->certificate_template_id) {
                Log::info("No certificate template set for course {$course->id}");
                return null;
            }

            // Cek apakah sertifikat sudah ada
            $existingCertificate = Certificate::where('course_id', $course->id)
                ->where('user_id', $user->id)
                ->first();

            if ($existingCertificate) {
                Log::info("Certificate already exists for user {$user->id} in course {$course->id}");
                return $existingCertificate;
            }

            // Panggil ProgressController untuk generate certificate
            $progressController = new \App\Http\Controllers\ProgressController();
            $reflection = new \ReflectionClass($progressController);
            $method = $reflection->getMethod('generateCertificate');
            $method->setAccessible(true);

            $certificate = $method->invoke($progressController, $course, $user);

            if ($certificate) {
                Log::info("Certificate generated successfully for user {$user->id} in course {$course->id}");
                return $certificate;
            } else {
                Log::warning("Failed to generate certificate for user {$user->id} in course {$course->id}");
                return null;
            }
        } catch (\Exception $e) {
            Log::error("Error generating certificate for user {$user->id} in course {$course->id}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Display user's certificates
     */
    public function index()
    {
        $user = Auth::user();

        $certificates = Certificate::where('user_id', $user->id)
            ->with(['course', 'certificateTemplate'])
            ->orderBy('issued_at', 'desc')
            ->paginate(10);

        return view('certificates.index', compact('certificates'));
    }

    /**
     * Show specific certificate for authenticated user
     */
    public function show(Certificate $certificate)
    {
        $this->authorize('view', $certificate);

        $certificate->load(['course', 'user', 'certificateTemplate']);

        return view('certificates.show', compact('certificate'));
    }

    /**
     * Download certificate PDF
     */
    public function download(Certificate $certificate)
    {
        $this->authorize('view', $certificate);

        if (!$certificate->fileExists()) {
            return back()->with('error', 'File sertifikat tidak ditemukan.');
        }

        $fileName = 'Certificate-' . $certificate->course->title . '-' . $certificate->user->name . '.pdf';

        return Storage::disk('public')->download($certificate->path, $fileName);
    }

    /**
     * Public certificate verification (no auth required)
     */
    public function verify($code)
    {
        $certificate = Certificate::where('certificate_code', $code)
            ->with(['user', 'course', 'certificateTemplate'])
            ->first();

        if (!$certificate) {
            return view('certificates.verify-error')->with('error', 'Kode sertifikat tidak valid.');
        }

        return view('certificates.verify', compact('certificate'));
    }

    /**
     * Public download for verified certificate (no auth required)
     */
    public function publicDownload($code)
    {
        $certificate = Certificate::where('certificate_code', $code)->first();

        if (!$certificate || !$certificate->fileExists()) {
            abort(404, 'Sertifikat tidak ditemukan.');
        }

        $fileName = 'Certificate-' . $certificate->certificate_code . '.pdf';

        return Storage::disk('public')->download($certificate->path, $fileName);
    }

    /**
     * Generate certificate manually (for admin/instructor)
     */
    public function generate(Request $request, Course $course, User $user)
    {
        $this->authorize('update', $course);

        // Check if user is enrolled
        if (!$course->enrolledUsers()->where('user_id', $user->id)->exists()) {
            return back()->with('error', 'User tidak terdaftar dalam kursus ini.');
        }

        // Check if certificate already exists
        $existingCertificate = Certificate::where('course_id', $course->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingCertificate) {
            return back()->with('error', 'Sertifikat untuk user ini sudah ada.');
        }

        // Check if course has template
        if (!$course->certificate_template_id) {
            return back()->with('error', 'Kursus belum memiliki template sertifikat.');
        }

        try {
            // Use the same logic as ProgressController
            $progressController = new \App\Http\Controllers\ProgressController();
            $reflection = new \ReflectionClass($progressController);
            $method = $reflection->getMethod('generateCertificate');
            $method->setAccessible(true);

            $certificate = $method->invoke($progressController, $course, $user);

            if ($certificate) {
                return back()->with('success', 'Sertifikat berhasil dibuat untuk ' . $user->name);
            } else {
                return back()->with('error', 'Gagal membuat sertifikat.');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Regenerate certificate (delete old and create new)
     */
    public function regenerate(Certificate $certificate)
    {
        $this->authorize('update', $certificate->course);

        try {
            $course = $certificate->course;
            $user = $certificate->user;

            // Delete old certificate file
            if ($certificate->fileExists()) {
                Storage::disk('public')->delete($certificate->path);
            }

            // Delete old certificate record
            $certificate->delete();

            // Generate new certificate
            $progressController = new \App\Http\Controllers\ProgressController();
            $reflection = new \ReflectionClass($progressController);
            $method = $reflection->getMethod('generateCertificate');
            $method->setAccessible(true);

            $newCertificate = $method->invoke($progressController, $course, $user);

            if ($newCertificate) {
                return back()->with('success', 'Sertifikat berhasil dibuat ulang.');
            } else {
                return back()->with('error', 'Gagal membuat ulang sertifikat.');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Delete certificate
     */
    public function destroy(Certificate $certificate)
    {
        $this->authorize('delete', $certificate);

        try {
            // Delete file if exists
            if ($certificate->fileExists()) {
                Storage::disk('public')->delete($certificate->path);
            }

            // Delete record
            $certificate->delete();

            return back()->with('success', 'Sertifikat berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus sertifikat: ' . $e->getMessage());
        }
    }

    /**
     * Get certificates for a specific course (for instructor view)
     */
    public function courseIndex(Course $course)
    {
        $this->authorize('viewProgress', $course);

        $certificates = Certificate::where('course_id', $course->id)
            ->with(['user', 'certificateTemplate'])
            ->orderBy('issued_at', 'desc')
            ->paginate(15);

        return view('certificates.course-index', compact('course', 'certificates'));
    }

    /**
     * Bulk generate certificates for all eligible users in a course
     */
    public function bulkGenerate(Course $course)
    {
        $this->authorize('update', $course);

        if (!$course->certificate_template_id) {
            return back()->with('error', 'Kursus belum memiliki template sertifikat.');
        }

        $eligibleUsers = [];
        $enrolledUsers = $course->enrolledUsers;

        foreach ($enrolledUsers as $user) {
            // Check if user meets criteria and doesn't already have certificate
            $progress = $user->courseProgress($course);
            $allGraded = $user->areAllGradedItemsMarked($course);
            $hasCertificate = Certificate::where('course_id', $course->id)
                ->where('user_id', $user->id)
                ->exists();

            if ($progress >= 100 && $allGraded && !$hasCertificate) {
                $eligibleUsers[] = $user;
            }
        }

        if (empty($eligibleUsers)) {
            return back()->with('info', 'Tidak ada peserta yang memenuhi syarat untuk mendapat sertifikat.');
        }

        $generated = 0;
        $progressController = new \App\Http\Controllers\ProgressController();
        $reflection = new \ReflectionClass($progressController);
        $method = $reflection->getMethod('generateCertificate');
        $method->setAccessible(true);

        foreach ($eligibleUsers as $user) {
            try {
                $certificate = $method->invoke($progressController, $course, $user);
                if ($certificate) {
                    $generated++;
                }
            } catch (\Exception $e) {
                \Log::error("Failed to generate certificate for user {$user->id}: " . $e->getMessage());
            }
        }

        return back()->with('success', "Berhasil membuat {$generated} sertifikat dari " . count($eligibleUsers) . " peserta yang memenuhi syarat.");
    }
}
