<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class EventOrganizerController extends Controller
{
    /**
     * Menampilkan halaman daftar kursus untuk Event Organizer.
     * ✅ OPTIMIZED: Pagination + Efficient Query + Caching
     */
    public function index(Request $request)
    {
        // ✅ OPTIMASI 1: Pagination untuk mengurangi data yang di-load sekaligus
        $perPage = 15; // 15 courses per page

        // ✅ OPTIMASI 2: Load courses dengan eager loading dan withCount
        $courses = Course::where('status', 'published')
            ->with('instructors')
            ->withCount(['enrolledUsers', 'lessons'])
            ->latest()
            ->paginate($perPage);

        // ✅ OPTIMASI 3: Batch calculate progress untuk semua courses di halaman ini
        // Hanya kalkulasi untuk courses yang ditampilkan (15 courses), bukan semua
        $courseIds = $courses->pluck('id');

        // Query efisien untuk mendapatkan progress per course
        $progressData = DB::table('courses')
            ->whereIn('courses.id', $courseIds)
            ->leftJoin('course_user as cu', 'courses.id', '=', 'cu.course_id')
            ->leftJoin('lessons as l', 'courses.id', '=', 'l.course_id')
            ->leftJoin('contents as c', 'l.id', '=', 'c.lesson_id')
            ->leftJoin('content_user as cu2', function($join) {
                $join->on('c.id', '=', 'cu2.content_id')
                     ->on('cu.user_id', '=', 'cu2.user_id')
                     ->where('cu2.completed', '=', 1);
            })
            ->select('courses.id',
                DB::raw('COUNT(DISTINCT cu.user_id) as total_users'),
                DB::raw('COUNT(DISTINCT c.id) as total_contents'),
                DB::raw('COUNT(DISTINCT cu2.id) as completed_count'))
            ->groupBy('courses.id')
            ->get()
            ->keyBy('id');

        // Attach calculated progress ke setiap course
        foreach ($courses as $course) {
            $data = $progressData->get($course->id);
            if ($data && $data->total_users > 0 && $data->total_contents > 0) {
                // Hitung rata-rata progress: (completed_count / (total_users * total_contents)) * 100
                $course->average_progress = round(($data->completed_count / ($data->total_users * $data->total_contents)) * 100);
            } else {
                $course->average_progress = 0;
            }
        }

        // ✅ OPTIMASI 4: Cache overall average progress selama 5 menit
        $cacheKey = 'eo_overall_average_progress';
        $overallAverageProgress = Cache::remember($cacheKey, 300, function () {
            // Query efisien untuk overall average dari SEMUA published courses
            $result = DB::table('courses')
                ->where('courses.status', 'published')
                ->leftJoin('course_user as cu', 'courses.id', '=', 'cu.course_id')
                ->leftJoin('lessons as l', 'courses.id', '=', 'l.course_id')
                ->leftJoin('contents as c', 'l.id', '=', 'c.lesson_id')
                ->leftJoin('content_user as cu2', function($join) {
                    $join->on('c.id', '=', 'cu2.content_id')
                         ->on('cu.user_id', '=', 'cu2.user_id')
                         ->where('cu2.completed', '=', 1);
                })
                ->select(
                    DB::raw('COUNT(DISTINCT cu.user_id) as total_users'),
                    DB::raw('COUNT(DISTINCT c.id) as total_contents'),
                    DB::raw('COUNT(DISTINCT cu2.id) as completed_count')
                )
                ->first();

            if ($result && $result->total_users > 0 && $result->total_contents > 0) {
                return round(($result->completed_count / ($result->total_users * $result->total_contents)) * 100);
            }

            return 0;
        });

        return view('event_organizer.courses_index', compact('courses', 'overallAverageProgress'));
    }
}