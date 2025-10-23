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

        $query = User::permission('attempt quizzes');

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
        $institutions = User::permission('attempt quizzes')
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

        $participants = User::permission('attempt quizzes')->get();

        // ✅ DEBUG: Log beberapa sample data untuk debugging (bisa diaktifkan saat testing)
        if (config('app.debug') && $participants->count() > 0) {
            $sampleParticipants = $participants->take(5);
            foreach ($sampleParticipants as $p) {
                if ($p->date_of_birth) {
                    try {
                        $birthDate = \Carbon\Carbon::parse($p->date_of_birth);
                        $age = $birthDate->age;
                        \Log::info("Participant Age Debug", [
                            'name' => $p->name,
                            'date_of_birth' => $p->date_of_birth,
                            'calculated_age' => $age,
                            'is_future' => $birthDate->isFuture()
                        ]);
                    } catch (\Exception $e) {
                        \Log::error("Participant Age Parse Error", [
                            'name' => $p->name,
                            'date_of_birth' => $p->date_of_birth,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }
        }

        // Gender Distribution (termasuk yang belum mengisi)
        $genderData = [
            'male' => $participants->where('gender', 'male')->count(),
            'female' => $participants->where('gender', 'female')->count(),
            'not_specified' => $participants->whereNull('gender')->count(),
        ];

        // ✅ FIXED: Age Distribution dengan validasi yang lebih baik
        $ageGroups = [
            '< 20' => 0,
            '20-25' => 0,
            '26-30' => 0,
            '31-35' => 0,
            '36-40' => 0,
            '> 40' => 0,
            'Tidak Diketahui' => 0,
        ];

        foreach ($participants as $participant) {
            if ($participant->date_of_birth) {
                try {
                    // Pastikan date_of_birth dalam format Carbon
                    $birthDate = \Carbon\Carbon::parse($participant->date_of_birth);

                    // Validasi: tanggal lahir tidak boleh di masa depan
                    if ($birthDate->isFuture()) {
                        $ageGroups['Tidak Diketahui']++;
                        continue;
                    }

                    // Validasi: umur tidak boleh lebih dari 120 tahun
                    $age = $birthDate->age;
                    if ($age > 120) {
                        $ageGroups['Tidak Diketahui']++;
                        continue;
                    }

                    // Kelompokkan berdasarkan umur
                    if ($age < 20) {
                        $ageGroups['< 20']++;
                    } elseif ($age >= 20 && $age <= 25) {
                        $ageGroups['20-25']++;
                    } elseif ($age >= 26 && $age <= 30) {
                        $ageGroups['26-30']++;
                    } elseif ($age >= 31 && $age <= 35) {
                        $ageGroups['31-35']++;
                    } elseif ($age >= 36 && $age <= 40) {
                        $ageGroups['36-40']++;
                    } else {
                        $ageGroups['> 40']++;
                    }
                } catch (\Exception $e) {
                    // Jika parsing gagal, masukkan ke "Tidak Diketahui"
                    $ageGroups['Tidak Diketahui']++;
                }
            } else {
                $ageGroups['Tidak Diketahui']++;
            }
        }

        // Institution Distribution (Top 10, termasuk yang belum mengisi)
        $institutionDataRaw = $participants
            ->groupBy(function($item) {
                return $item->institution_name ?? 'Belum Diisi';
            })
            ->map->count()
            ->sortDesc();

        $institutionData = $institutionDataRaw->take(10);

        // Occupation Distribution (Top 10, termasuk yang belum mengisi)
        $occupationDataRaw = $participants
            ->groupBy(function($item) {
                return $item->occupation ?? 'Belum Diisi';
            })
            ->map->count()
            ->sortDesc();

        $occupationData = $occupationDataRaw->take(10);

        // Registration Trend (Last 12 months)
        $registrationTrend = [];
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $count = User::permission('attempt quizzes')
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
            'not_specified' => $genderData['not_specified'],
            'with_complete_data' => $participants->whereNotNull('gender')
                ->whereNotNull('date_of_birth')
                ->whereNotNull('institution_name')
                ->whereNotNull('occupation')
                ->count(),
            'this_month' => User::permission('attempt quizzes')
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
