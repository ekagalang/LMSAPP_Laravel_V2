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
        if ($user->can('manage all courses')) {
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
        if ($user->can('manage all courses')) {
            return true;
        }

        if ($user->can('manage own courses') && $course->instructors->contains($user)) {
            return true;
        }

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
     * =================================================================
     * PENYESUAIAN: Tambahkan policy baru untuk duplikasi
     * =================================================================
     *
     * Menentukan siapa yang boleh MENDUPLIKASI kursus.
     * Hanya pengguna dengan izin 'manage all courses' (seperti EO) yang bisa.
     * Ini akan menyembunyikan tombol dari instruktur biasa.
     */
    public function duplicate(User $user): bool
    {
        return $user->can('manage all courses');
    }

    /**
     * Menentukan siapa yang boleh MENGUBAH kursus.
     */
    public function update(User $user, Course $course): bool
    {
        return $user->can('manage all courses');
    }

    /**
     * Menentukan siapa yang boleh MENGHAPUS kursus.
     */
    public function delete(User $user, Course $course): bool
    {
        return $user->can('manage all courses');
    }

    /**
     * Menentukan siapa yang boleh melihat gradebook.
     */
    public function viewGradebook(User $user, Course $course): bool
    {
        return $user->can('grade quizzes') && $course->instructors->contains($user);
    }

    /**
     * [BARU] Menentukan siapa yang boleh menilai (grade) kursus.
     */
    public function grade(User $user, Course $course): bool
    {
        return $course->instructors->contains($user);
    }
}
