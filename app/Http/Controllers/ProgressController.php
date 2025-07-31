<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Content;
use App\Models\Lesson;
use App\Models\Course; // <-- TAMBAHKAN USE STATEMENT
use App\Models\Certificate; // <-- TAMBAHKAN USE STATEMENT
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // <-- TAMBAHKAN USE STATEMENT
use Illuminate\Support\Facades\Log; // <-- TAMBAHKAN USE STATEMENT
use Illuminate\Support\Facades\Storage; // <-- TAMBAHKAN USE STATEMENT
use Barryvdh\DomPDF\Facade\Pdf;

class ProgressController extends Controller
{
    public function markContentAsCompleted(Content $content)
    {
        $user = Auth::user();
        $lesson = $content->lesson;
        $course = $lesson->course;

        // Tandai konten saat ini sebagai selesai
        $user->completedContents()->syncWithoutDetaching([
            $content->id => ['completed' => true, 'completed_at' => now()]
        ]);

        // Cek apakah lesson (materi) sekarang sudah selesai
        if ($user->hasCompletedAllContentsInLesson($lesson)) {
            $user->lessons()->syncWithoutDetaching([$lesson->id => ['status' => 'completed']]);
        }

        // =================================================================
        // PERUBAHAN UTAMA: Panggil fungsi pengecekan sertifikat
        // =================================================================
        $this->checkAndGenerateCertificate($course, $user);
        // =================================================================

        // Cari konten berikutnya dalam pelajaran yang sama berdasarkan urutan
        $nextContent = $lesson->contents()
            ->where('order', '>', $content->order)
            ->orderBy('order', 'asc')
            ->first();

        // Jika ada konten berikutnya, arahkan ke sana
        if ($nextContent) {
            return redirect()->route('contents.show', ['content' => $nextContent->id])
                   ->with('success', 'Lanjut ke konten berikutnya!');
        }

        // Jika tidak ada konten berikutnya (konten terakhir dalam pelajaran),
        // cek apakah semua pelajaran di kursus ini sudah selesai.
        $allLessonsCompleted = true;
        foreach ($course->lessons as $courseLesson) {
            if (!$user->hasCompletedLesson($courseLesson)) {
                $allLessonsCompleted = false;
                break;
            }
        }

        if ($allLessonsCompleted) {
             return redirect()->route('courses.show', $course->id)->with('success', 'Selamat! Anda telah menyelesaikan seluruh kursus ini.');
        }

        // Jika ini adalah konten terakhir dari pelajaran, tapi masih ada pelajaran lain,
        // kembalikan ke halaman kursus.
        return redirect()->route('courses.show', $course->id)->with('success', 'Selamat! Anda telah menyelesaikan pelajaran ini.');
    }

    public function markLessonAsCompleted(Lesson $lesson)
    {
        $user = Auth::user();

        // Tandai semua konten dalam pelajaran ini sebagai selesai juga
        $contentIds = $lesson->contents->pluck('id')->toArray();
        if (!empty($contentIds)) {
            $user->contents()->syncWithoutDetaching(
                array_fill_keys($contentIds, ['status' => 'completed'])
            );
        }

        $user->lessons()->syncWithoutDetaching([$lesson->id => ['status' => 'completed']]);

        return redirect()->back()->with('success', 'Pelajaran berhasil ditandai selesai!');
    }

    // =================================================================
    // FUNGSI BARU: Untuk memeriksa dan men-generate sertifikat
    // =================================================================
    private function checkAndGenerateCertificate(Course $course, User $user)
    {
        if (!$course->certificate_template_id) {
            return;
        }

        $progress = $user->courseProgress($course);

        if ($progress >= 100 && $user->areAllGradedItemsMarked($course)) {
            $existingCertificate = Certificate::where('course_id', $course->id)
                ->where('user_id', $user->id)
                ->first();

            if (!$existingCertificate) {
                $this->generateCertificate($course, $user);
            }
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
            $pdf = Pdf::loadView('reports.progress_pdf', [
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

    public function exportCourseProgressPdf(Course $course)
    {
        // 1. Ambil semua peserta dan semua konten kursus dengan efisien
        $participants = $course->enrolledUsers()->orderBy('name')->get();
        $lessons = $course->lessons()->with(['contents' => function ($query) {
            $query->orderBy('order');
        }])->orderBy('order')->get();
        
        // Buat koleksi datar dari semua ID konten untuk kursus ini
        $allContentIds = $lessons->pluck('contents.*.id')->flatten()->unique();

        // 2. Ambil semua data penyelesaian untuk kursus ini dalam satu query
        // Kuncinya adalah string "user_id-content_id" untuk pencarian cepat
        $completionsLookup = DB::table('content_user')
            ->whereIn('user_id', $participants->pluck('id'))
            ->whereIn('content_id', $allContentIds)
            ->where('completed', true)
            ->get()
            ->keyBy(fn ($item) => $item->user_id . '-' . $item->content_id);

        // 3. Siapkan struktur data yang mendetail untuk dikirim ke view
        $participantsProgress = [];
        $totalContentsCount = $allContentIds->count();

        foreach ($participants as $participant) {
            $completedCount = 0;
            $detailedLessons = [];

            foreach ($lessons as $lesson) {
                $contentsWithStatus = [];
                foreach ($lesson->contents as $content) {
                    $isCompleted = $completionsLookup->has($participant->id . '-' . $content->id);
                    if ($isCompleted) {
                        $completedCount++;
                    }
                    $contentsWithStatus[] = (object)[
                        'title' => $content->title,
                        'is_completed' => $isCompleted,
                    ];
                }
                $detailedLessons[] = (object)[
                    'title' => $lesson->title,
                    'contents' => $contentsWithStatus,
                ];
            }

            $participantsProgress[] = (object)[
                'name' => $participant->name,
                'email' => $participant->email,
                'progressPercentage' => $totalContentsCount > 0 ? round(($completedCount / $totalContentsCount) * 100) : 0,
                'lessons' => $detailedLessons,
            ];
        }

        // 4. Siapkan data akhir untuk view PDF
        $data = [
            'course' => $course,
            'participantsProgress' => $participantsProgress,
            'date' => now()->translatedFormat('d F Y'),
        ];

        // 5. Buat dan unduh PDF
        $pdf = Pdf::loadView('reports.progress_pdf', $data);
        $pdf->setPaper('a4', 'portrait');

        $fileName = 'laporan-progres-lengkap-' . Str::slug($course->title) . '.pdf';

        return $pdf->download($fileName);
    }
}