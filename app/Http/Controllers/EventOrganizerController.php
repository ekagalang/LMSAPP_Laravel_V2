<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventOrganizerController extends Controller
{
    /**
     * Menampilkan halaman daftar kursus untuk Event Organizer.
     */
    public function index()
    {
        // Ambil semua kursus yang dipublikasikan
        $courses = Course::where('status', 'published')
            ->with('instructors')
            // Eager load counts untuk efisiensi
            ->withCount(['enrolledUsers', 'lessons'])
            ->latest()
            ->get();

        // =================================================================
        // PERBAIKAN: Kalkulasi rata-rata progres
        // =================================================================
        $totalProgress = 0;
        $courseWithProgressCount = 0;

        foreach ($courses as $course) {
            // Panggil method helper untuk mendapatkan rata-rata progres per kursus
            $course->average_progress = $course->getAverageProgress();
            if ($course->enrolled_users_count > 0) {
                $totalProgress += $course->average_progress;
                $courseWithProgressCount++;
            }
        }

        $overallAverageProgress = $courseWithProgressCount > 0 ? round($totalProgress / $courseWithProgressCount) : 0;
            
        return view('event_organizer.courses_index', compact('courses', 'overallAverageProgress'));
    }

    public function getAverageProgress(): int
    {
        $totalUsers = $this->enrolledUsers()->count();
        if ($totalUsers === 0) {
            return 0;
        }

        $totalProgressSum = 0;
        foreach ($this->enrolledUsers as $user) {
            // Asumsi ada method getProgressForCourse di model User
            $progressData = $user->getProgressForCourse($this);
            $totalProgressSum += $progressData['progress_percentage'];
        }

        return round($totalProgressSum / $totalUsers);
    }
}