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
        // ✅ Menggunakan izin yang ada di file Anda: 'view courses'
        return $user->can('view courses');
    }

    /**
     * Menentukan siapa yang boleh MELIHAT DETAIL sebuah kursus.
     */
    public function view(User $user, Course $course): bool
    {
        // Jika kursus sudah publish, semua yang terdaftar boleh lihat.
        if ($course->status === 'published') {
            return true;
        }

        // Jika draft, hanya yang punya izin yang sesuai.
        // Anda bisa memilih 'manage own courses' atau 'manage all courses'.
        return $user->can('manage own courses') || $user->can('manage all courses');
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
        // ✅ Menggunakan izin yang ada di file Anda: 'manage all courses'
        return $user->can('manage all courses');
    }

    /**
     * Menentukan siapa yang boleh MENGUBAH kursus.
     */
    public function update(User $user, Course $course): bool
    {
        // ✅ LOGIKA BARU: Izinkan jika user terdaftar sebagai salah satu instruktur di kursus ini
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

    public function viewGradebook(User $user, Course $course): bool
    {
        // Pengguna bisa melihat gradebook jika punya izin 'grade quizzes'
        // DAN merupakan instruktur untuk kursus ini.
        return $user->can('grade quizzes') && $user->isInstructorFor($course);
    }
}