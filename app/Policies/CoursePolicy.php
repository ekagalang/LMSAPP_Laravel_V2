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
        // Siapa saja yang bisa melihat daftar kursus?
        // User yang punya salah satu dari permission ini.
        return $user->hasPermissionTo('manage all courses') || $user->hasPermissionTo('manage own courses');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Course $course): bool
    {
        // Semua pengguna yang terdaftar bisa melihat detail kursus.
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Hanya user dengan permission 'manage all courses' (seperti admin) 
        // atau 'manage own courses' (seperti instruktur) yang bisa membuat kursus.
        return $user->hasPermissionTo('manage all courses') || $user->hasPermissionTo('manage own courses');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Course $course): bool
    {
        // Admin bisa mengedit semua kursus.
        if ($user->hasPermissionTo('manage all courses')) {
            return true;
        }
        // Instruktur hanya bisa mengedit kursusnya sendiri.
        if ($user->hasPermissionTo('manage own courses')) {
            return $user->id === $course->user_id;
        }
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Course $course): bool
    {
        // Sama seperti update, hanya admin atau pemilik kursus yang bisa menghapus.
        if ($user->hasPermissionTo('manage all courses')) {
            return true;
        }
        if ($user->hasPermissionTo('manage own courses')) {
            return $user->id === $course->user_id;
        }
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Course $course): bool
    {
        return $user->hasPermissionTo('manage all courses');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Course $course): bool
    {
        return $user->hasPermissionTo('manage all courses');
    }
}
