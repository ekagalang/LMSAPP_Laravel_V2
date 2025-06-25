<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CoursePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isInstructor() || $user->isParticipant();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Course $course): bool
    {
        // Semua user yang bisa melihat daftar kursus (index) juga bisa melihat detailnya.
        // Namun, di tahap lanjut, peserta mungkin hanya bisa melihat kursus yang di-enroll.
        // Untuk saat ini, kita biarkan semua bisa melihat detailnya.
        return $user->isAdmin() || $user->isInstructor() || $user->isParticipant();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Hanya admin atau instruktur yang bisa membuat kursus
        return $user->isAdmin() || $user->isInstructor();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Course $course): bool
    {
        // Admin bisa update semua kursus
        // Instruktur hanya bisa update kursus yang dia buat
        return $user->isAdmin() || ($user->isInstructor() && $user->id === $course->user_id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Course $course): bool
    {
        // Admin bisa delete semua kursus
        // Instruktur hanya bisa delete kursus yang dia buat
        return $user->isAdmin() || ($user->isInstructor() && $user->id === $course->user_id);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Course $course): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Course $course): bool
    {
        return false;
    }
}