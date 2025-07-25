<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Content;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class ProgressController extends Controller
{
    public function markContentAsCompleted(Content $content)
    {
        $user = Auth::user();

        // Tandai konten saat ini sebagai selesai
        $user->completedContents()->syncWithoutDetaching([
            $content->id => ['completed' => true, 'completed_at' => now()]
        ]);

        $lesson = $content->lesson;
        $course = $lesson->course;

        // =================================================================
        // PERUBAHAN DIMULAI DI SINI
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
            if (!$user->hasCompletedLesson($courseLesson->id)) {
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
        
        // =================================================================
        // PERUBAHAN SELESAI DI SINI
        // =================================================================
    }

    public function markLessonAsCompleted(Lesson $lesson)
    {
        $user = Auth::user();
        // Pastikan user sudah enroll kursus induk dari pelajaran ini
        // if (!$user->isEnrolled($lesson->course)) {
        //     return redirect()->back()->with('error', 'Anda harus terdaftar di kursus ini untuk menandai progres.');
        // }

        // Tandai semua konten dalam pelajaran ini sebagai selesai juga
        $contentIds = $lesson->contents->pluck('id')->toArray();
        if (!empty($contentIds)) {
            $user->completedContents()->syncWithoutDetaching(
                array_fill_keys($contentIds, ['completed' => true, 'completed_at' => now()])
            );
        }

        $user->completedLessons()->syncWithoutDetaching([
            $lesson->id => ['completed' => true, 'completed_at' => now()]
        ]);


        return redirect()->back()->with('success', 'Pelajaran berhasil ditandai selesai!');
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