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
        // Pastikan user sudah enroll kursus induk dari konten ini (akan diimplementasikan lebih lanjut)
        // if (!$user->isEnrolled($content->lesson->course)) {
        //     return redirect()->back()->with('error', 'Anda harus terdaftar di kursus ini untuk menandai progres.');
        // }

        $user->completedContents()->syncWithoutDetaching([
            $content->id => ['completed' => true, 'completed_at' => now()]
        ]);

        // Opsional: Cek apakah semua konten di pelajaran sudah selesai, jika iya, tandai pelajaran selesai
        $lesson = $content->lesson;
        $allContentsCompleted = true;
        foreach ($lesson->contents as $lessonContent) {
            if (!$lessonContent->isCompletedByUser($user->id)) {
                $allContentsCompleted = false;
                break;
            }
        }

        if ($allContentsCompleted) {
            $user->completedLessons()->syncWithoutDetaching([
                $lesson->id => ['completed' => true, 'completed_at' => now()]
            ]);
        }

        return redirect()->back()->with('success', 'Konten berhasil ditandai selesai!');
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