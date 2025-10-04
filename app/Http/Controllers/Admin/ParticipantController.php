<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ParticipantController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $query = User::role('participant');

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('institution_name', 'like', "%{$search}%")
                  ->orWhere('occupation', 'like', "%{$search}%");
            });
        }

        // Filter by gender
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        // Filter by institution
        if ($request->filled('institution')) {
            $query->where('institution_name', $request->institution);
        }

        $participants = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get unique institutions for filter
        $institutions = User::role('participant')
            ->whereNotNull('institution_name')
            ->distinct()
            ->pluck('institution_name')
            ->sort();

        return view('admin.participants.index', compact('participants', 'institutions'));
    }

    public function show(User $user)
    {
        $this->authorize('view', $user);

        // Get enrolled courses with progress
        $enrolledCourses = $user->enrolledCourses()
            ->with('lessons')
            ->get()
            ->map(function ($course) use ($user) {
                $progress = $user->getProgressForCourse($course);
                return [
                    'course' => $course,
                    'progress' => $progress
                ];
            });

        return view('admin.participants.show', compact('user', 'enrolledCourses'));
    }

    public function analytics()
    {
        $this->authorize('viewAny', User::class);

        $participants = User::role('participant')->get();

        // Gender Distribution
        $genderData = [
            'male' => $participants->where('gender', 'male')->count(),
            'female' => $participants->where('gender', 'female')->count(),
        ];

        // Age Distribution
        $ageGroups = [
            '< 20' => 0,
            '20-25' => 0,
            '26-30' => 0,
            '31-35' => 0,
            '36-40' => 0,
            '> 40' => 0,
        ];

        foreach ($participants as $participant) {
            if ($participant->date_of_birth) {
                $age = now()->diffInYears($participant->date_of_birth);
                if ($age < 20) $ageGroups['< 20']++;
                elseif ($age <= 25) $ageGroups['20-25']++;
                elseif ($age <= 30) $ageGroups['26-30']++;
                elseif ($age <= 35) $ageGroups['31-35']++;
                elseif ($age <= 40) $ageGroups['36-40']++;
                else $ageGroups['> 40']++;
            }
        }

        // Institution Distribution (Top 10)
        $institutionData = $participants
            ->whereNotNull('institution_name')
            ->groupBy('institution_name')
            ->map->count()
            ->sortDesc()
            ->take(10);

        // Occupation Distribution (Top 10)
        $occupationData = $participants
            ->whereNotNull('occupation')
            ->groupBy('occupation')
            ->map->count()
            ->sortDesc()
            ->take(10);

        // Registration Trend (Last 12 months)
        $registrationTrend = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $count = User::role('participant')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
            $registrationTrend[$month->format('M Y')] = $count;
        }

        // Total Statistics
        $stats = [
            'total' => $participants->count(),
            'male' => $genderData['male'],
            'female' => $genderData['female'],
            'this_month' => User::role('participant')
                ->whereYear('created_at', now()->year)
                ->whereMonth('created_at', now()->month)
                ->count(),
        ];

        return view('admin.participants.analytics', compact(
            'stats',
            'genderData',
            'ageGroups',
            'institutionData',
            'occupationData',
            'registrationTrend'
        ));
    }
}
