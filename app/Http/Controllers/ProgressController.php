<?php

namespace App\Http\Controllers;

use App\Models\Content;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
}