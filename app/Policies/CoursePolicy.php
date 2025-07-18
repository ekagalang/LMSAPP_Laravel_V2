<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CoursePolicy
{
    /**
     * Pengecekan ini berjalan sebelum method lainnya.
     * Jika user adalah super-admin, semua aksi diizinkan.
     */
    public function before(User $user, string $ability): bool|null
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }
        return null;
    }

    /**
     * Menentukan siapa yang boleh MELIHAT MENU "Manajemen Kursus".
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view courses');
    }

    /**
     * Menentukan siapa yang boleh MELIHAT DETAIL sebuah kursus.
     */
    public function view(User $user, Course $course): bool
    {
        // Jika user adalah instruktur atau admin, izinkan.
        if ($user->can('manage own courses') || $user->can('manage all courses')) {
            return true;
        }

        // Jika kursus sudah publish DAN user terdaftar di kursus tersebut, izinkan.
        if ($course->status === 'published' && $course->enrolledUsers->contains($user)) {
            return true;
        }

        return false;
    }
    
    /**
     * Menentukan siapa yang boleh melihat halaman progres.
     */
    public function viewProgress(User $user, Course $course): bool
    {
        // Izinkan jika user adalah instruktur ATAU punya izin lihat laporan
        return $course->instructors->contains($user) || $user->can('view progress reports');
    }

    /**
     * Menentukan siapa yang boleh MEMBUAT kursus.
     */
    public function create(User $user): bool
    {
        return $user->can('manage all courses');
    }

    /**
     * Menentukan siapa yang boleh MENGUBAH kursus.
     */
    public function update(User $user, Course $course): bool
    {
        // Izinkan jika user terdaftar sebagai salah satu instruktur di kursus ini
        return $user->can('manage own courses') && $course->instructors->contains($user);
    }

    /**
     * Menentukan siapa yang boleh MENGHAPUS kursus.
     */
    public function delete(User $user, Course $course): bool
    {
        // Logikanya sama dengan update
        return $user->can('manage own courses') && $course->instructors->contains($user);
    }

    /**
     * Menentukan siapa yang boleh melihat gradebook.
     */
    public function viewGradebook(User $user, Course $course): bool
    {
        // Pengguna bisa melihat gradebook jika punya izin 'grade quizzes'
        // DAN merupakan instruktur untuk kursus ini.
        return $user->can('grade quizzes') && $course->instructors->contains($user);
    }

    /**
     * [BARU] Menentukan siapa yang boleh menilai (grade) kursus.
     * Ini akan dipanggil oleh GradebookController.
     */
    public function grade(User $user, Course $course): bool
    {
        // Izinkan jika pengguna adalah salah satu instruktur yang ditugaskan ke kursus ini.
        return $course->instructors->contains($user);
    }
}
