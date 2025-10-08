<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TokenEnrollmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Enroll using course token
     */
    public function enrollCourse(Request $request)
    {
        $request->validate([
            'token' => 'required|string|max:20'
        ]);

        $token = strtoupper(trim($request->token));
        $user = Auth::user();

        // Find course by token
        $course = Course::where('enrollment_token', $token)
            ->where('token_enabled', true)
            ->first();

        if (!$course) {
            return back()->withErrors([
                'token' => 'Token tidak valid atau sudah tidak aktif.'
            ]);
        }

        // Check if token expired
        if (!$course->isTokenValid()) {
            return back()->withErrors([
                'token' => 'Token sudah kadaluarsa.'
            ]);
        }

        // Check if already enrolled
        if ($course->enrolledUsers()->where('users.id', $user->id)->exists()) {
            return back()->withErrors([
                'token' => 'Anda sudah terdaftar di course ini.'
            ]);
        }

        try {
            DB::beginTransaction();

            // Enroll user to course
            $course->enrolledUsers()->attach($user->id);

            DB::commit();

            return redirect()->route('courses.show', $course)
                ->with('success', "Berhasil bergabung dengan course: {$course->title}");
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors([
                'token' => 'Gagal mendaftar: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Unified enrollment - auto-detect course or class token
     */
    public function enroll(Request $request)
    {
        $request->validate([
            'token' => 'required|string|max:20'
        ]);

        $token = strtoupper(trim($request->token));
        $user = Auth::user();

        // Try to find course first
        $course = Course::where('enrollment_token', $token)
            ->where('token_enabled', true)
            ->first();

        if ($course) {
            // It's a course token
            if (!$course->isTokenValid()) {
                return back()->withErrors([
                    'token' => 'Token sudah kadaluarsa.'
                ]);
            }

            if ($course->enrolledUsers()->where('users.id', $user->id)->exists()) {
                return back()->withErrors([
                    'token' => 'Anda sudah terdaftar di kursus ini.'
                ]);
            }

            try {
                DB::beginTransaction();
                $course->enrolledUsers()->attach($user->id);
                DB::commit();

                return redirect()->route('courses.show', $course)
                    ->with('success', "Berhasil bergabung dengan kursus: {$course->title}");
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->withErrors([
                    'token' => 'Gagal mendaftar: ' . $e->getMessage()
                ]);
            }
        }

        // Try to find class
        $class = CourseClass::where('enrollment_token', $token)
            ->where('token_enabled', true)
            ->with('course')
            ->first();

        if ($class) {
            // It's a class token
            if (!$class->isTokenValid()) {
                return back()->withErrors([
                    'token' => 'Token kelas sudah kadaluarsa.'
                ]);
            }

            $course = $class->course;

            if (!$class->hasAvailableSlots()) {
                return back()->withErrors([
                    'token' => "Kelas sudah penuh. Maksimal {$class->max_participants} peserta."
                ]);
            }

            if ($class->participants()->where('users.id', $user->id)->exists()) {
                return back()->withErrors([
                    'token' => 'Anda sudah terdaftar di kelas ini.'
                ]);
            }

            try {
                DB::beginTransaction();

                // First, enroll to course if not already enrolled
                if (!$course->enrolledUsers()->where('users.id', $user->id)->exists()) {
                    $course->enrolledUsers()->attach($user->id);
                }

                // Then, enroll to class
                $class->participants()->attach($user->id);

                DB::commit();

                return redirect()->route('courses.show', $course)
                    ->with('success', "Berhasil bergabung dengan kelas: {$class->name} di kursus {$course->title}");
            } catch (\Exception $e) {
                DB::rollBack();
                return back()->withErrors([
                    'token' => 'Gagal mendaftar ke kelas: ' . $e->getMessage()
                ]);
            }
        }

        // Token not found
        return back()->withErrors([
            'token' => 'Token tidak valid atau sudah tidak aktif.'
        ]);
    }

    /**
     * Enroll using class token
     */
    public function enrollClass(Request $request)
    {
        $request->validate([
            'token' => 'required|string|max:20'
        ]);

        $token = strtoupper(trim($request->token));
        $user = Auth::user();

        // Find class by token
        $class = CourseClass::where('enrollment_token', $token)
            ->where('token_enabled', true)
            ->with('course')
            ->first();

        if (!$class) {
            return back()->withErrors([
                'token' => 'Token kelas tidak valid atau sudah tidak aktif.'
            ]);
        }

        // Check if token expired
        if (!$class->isTokenValid()) {
            return back()->withErrors([
                'token' => 'Token kelas sudah kadaluarsa.'
            ]);
        }

        $course = $class->course;

        // Check if class is full
        if (!$class->hasAvailableSlots()) {
            return back()->withErrors([
                'token' => "Kelas sudah penuh. Maksimal {$class->max_participants} peserta."
            ]);
        }

        // Check if already enrolled in this class
        if ($class->participants()->where('users.id', $user->id)->exists()) {
            return back()->withErrors([
                'token' => 'Anda sudah terdaftar di kelas ini.'
            ]);
        }

        try {
            DB::beginTransaction();

            // First, enroll to course if not already enrolled
            if (!$course->enrolledUsers()->where('users.id', $user->id)->exists()) {
                $course->enrolledUsers()->attach($user->id);
            }

            // Then, enroll to class
            $class->participants()->attach($user->id);

            DB::commit();

            return redirect()->route('courses.show', $course)
                ->with('success', "Berhasil bergabung dengan kelas: {$class->name} di course {$course->title}");
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withErrors([
                'token' => 'Gagal mendaftar ke kelas: ' . $e->getMessage()
            ]);
        }
    }
}
