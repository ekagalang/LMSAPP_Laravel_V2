<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use App\Models\EssaySubmission;
use App\Models\Feedback;
use App\Models\Certificate; // <-- TAMBAHKAN USE STATEMENT
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // <-- TAMBAHKAN USE STATEMENT
use Illuminate\Support\Facades\Storage; // <-- TAMBAHKAN USE STATEMENT
use PDF;

class GradebookController extends Controller
{
    /**
     * Menampilkan halaman Gradebook terpusat dengan filter dan tabs.
     */
    public function index(Request $request, Course $course)
    {
        $this->authorize('viewGradebook', $course);
        $user = Auth::user();

        $allCoursesForFilter = collect();
        if ($user->hasRole('super-admin')) {
            $allCoursesForFilter = Course::orderBy('title')->get();
        } elseif ($user->hasRole('instructor')) {
            $allCoursesForFilter = Course::whereHas('instructors', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->orderBy('title')->get();
        }

        $participantsQuery = $course->enrolledUsers();

        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->search;
            $participantsQuery->where(function ($query) use ($searchTerm) {
                $query->where('name', 'like', '%' . $searchTerm . '%')
                      ->orWhere('email', 'like', '%' . $searchTerm . '%');
            });
        }
        $participants = $participantsQuery->with(['feedback' => function ($query) use ($course) {
            $query->where('course_id', $course->id);
        }])->get();

        $essayContentIds = $course->lessons()->with('contents')
            ->get()->pluck('contents')->flatten()->where('type', 'essay')->pluck('id');

        $participantsWithEssaysQuery = $course->enrolledUsers()
            ->whereHas('essaySubmissions', fn($q) => $q->whereIn('content_id', $essayContentIds));

        if ($request->has('search') && $request->search != '') {
            $searchTerm = $request->input('search');
            $participantsWithEssaysQuery->where(function ($query) use ($searchTerm) {
                $query->where('name', 'like', '%' . $searchTerm . '%')
                      ->orWhere('email', 'like', '%' . $searchTerm . '%');
            });
        }
        $participantsWithEssays = $participantsWithEssaysQuery->get();

        return view('gradebook.index', compact('course', 'participants', 'participantsWithEssays', 'allCoursesForFilter'));
    }

    /**
     * Menampilkan semua jawaban esai dari satu peserta.
     */
    public function showUserEssays(Course $course, User $user)
    {
        // PERBAIKAN: Menggunakan 'grade' sebagai otorisasi, bukan 'update'
        $this->authorize('grade', $course);

        $essayContentIds = $course->lessons()->with('contents')
            ->get()->pluck('contents')->flatten()->where('type', 'essay')->pluck('id');

        $submissions = EssaySubmission::with('content')
            ->where('user_id', $user->id)
            ->whereIn('content_id', $essayContentIds)
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('gradebook.user_essays', compact('course', 'user', 'submissions'));
    }

    /**
     * Menyimpan nilai dan feedback untuk sebuah jawaban esai.
     */
    public function storeEssayGrade(Request $request, EssaySubmission $submission)
    {
        $this->authorize('grade', $submission->content->lesson->course);

        $validated = $request->validate([
            'score' => 'required|integer|min:0|max:100',
            'feedback' => 'nullable|string',
        ]);

        $submission->update($validated + ['graded_at' => now(), 'status' => 'graded']);

        // =================================================================
        // PERUBAHAN UTAMA: Panggil fungsi pengecekan sertifikat
        // =================================================================
        $course = $submission->content->lesson->course;
        $user = $submission->user;
        $this->checkAndGenerateCertificate($course, $user);
        // =================================================================

        return redirect()->route('gradebook.user_essays', [
            'course' => $submission->content->lesson->course->id,
            'user' => $submission->user_id
        ])->with('success', 'Nilai untuk ' . $submission->user->name . ' berhasil disimpan.');
    }

    /**
     * Menyimpan feedback umum untuk seorang peserta.
     */
    public function storeFeedback(Request $request, Course $course, User $user)
    {
        // PERBAIKAN: Menggunakan 'grade' sebagai otorisasi, bukan 'update'
        $this->authorize('grade', $course);

        $request->validate(['feedback' => 'required|string']);

        Feedback::updateOrCreate(
            ['course_id' => $course->id, 'user_id' => $user->id],
            ['feedback' => $request->feedback, 'instructor_id' => auth()->id()]
        );

        return redirect()->back()->with('success', 'Feedback untuk ' . $user->name . ' berhasil disimpan.');
    }

    // =================================================================
    // FUNGSI BARU: Untuk memeriksa dan men-generate sertifikat
    // =================================================================
    private function checkAndGenerateCertificate(Course $course, User $user)
    {
        Log::info("Memulai pengecekan sertifikat untuk user: {$user->id} di course: {$course->id}");

        if (!$course->certificate_template_id) {
            Log::warning("Pengecekan dihentikan: Course {$course->id} tidak punya template sertifikat.");
            return;
        }

        $progress = $user->courseProgress($course);
        Log::info("Progres user {$user->id}: {$progress}%");

        $allGraded = $user->areAllGradedItemsMarked($course);
        Log::info("Apakah semua item sudah dinilai? " . ($allGraded ? 'Ya' : 'Tidak'));

        if ($progress >= 100 && $allGraded) {
            Log::info("SYARAT TERPENUHI: Progres 100% dan semua item sudah dinilai.");
            $existingCertificate = Certificate::where('course_id', $course->id)
                ->where('user_id', $user->id)
                ->first();

            if (!$existingCertificate) {
                Log::info("Sertifikat belum ada, memulai proses generate...");
                $this->generateCertificate($course, $user);
            } else {
                Log::info("Sertifikat sudah ada, tidak perlu generate ulang.");
            }
        } else {
            Log::info("SYARAT TIDAK TERPENUHI. Proses generate sertifikat tidak dijalankan.");
        }
    }

    private function generateCertificate(Course $course, User $user)
    {
        $template = $course->certificateTemplate;
        if (!$template) {
            Log::warning("Certificate generation skipped for user {$user->id} in course {$course->id}: No template found.");
            return;
        }

        Log::info("Generating certificate for user {$user->id} in course {$course->id}");

        try {
            // Pastikan view 'reports.progress_pdf' ada dan sesuai
            // atau ganti dengan view template sertifikat Anda, misal 'certificates.template'
            $pdf = PDF::loadView('reports.progress_pdf', [
                'user' => $user,
                'course' => $course,
                'template' => $template,
            ]);

            $fileName = 'certificate-' . $user->id . '-' . $course->id . '-' . time() . '.pdf';
            $filePath = 'certificates/' . $fileName;

            Storage::disk('public')->put($filePath, $pdf->output());

            Certificate::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
                'certificate_template_id' => $template->id,
                'path' => $filePath,
                'issued_at' => now(),
            ]);
            Log::info("Certificate generated successfully for user {$user->id} in course {$course->id}");
        } catch (\Exception $e) {
            Log::error("Certificate generation failed for user {$user->id} in course {$course->id}: " . $e->getMessage());
        }
    }
    // =================================================================
}
