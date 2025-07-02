<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CoursePolicy
{
    /**
     * Determine whether the user can view any models.
     * Metode ini dipanggil saat membuka halaman "Manajemen Kursus".
     */
    public function viewAny(User $user): bool
    {
        // PERBAIKAN: Gunakan permission yang sudah kita definisikan.
        // Instruktur dan Super Admin akan memiliki salah satu dari permission ini.
        return $user->hasPermissionTo('manage all courses') || $user->hasPermissionTo('manage own courses');
    }

    /**
     * Determine whether the user can view the model.
     * Metode ini untuk melihat detail satu kursus.
     */
    public function view(User $user, Course $course): bool
    {
        // Semua pengguna yang terdaftar bisa melihat detail kursus.
        return true;
    }

    /**
     * Determine whether the user can create models.
     * Metode ini untuk menampilkan tombol "Tambah Kursus Baru".
     */
    public function create(User $user): bool
    {
        // PERBAIKAN: Gunakan permission yang sama dengan viewAny.
        return $user->hasPermissionTo('manage all courses') || $user->hasPermissionTo('manage own courses');
    }

    /**
     * Determine whether the user can update the model.
     * Metode ini untuk menampilkan tombol "Edit".
     */
    public function update(User $user, Course $course): bool
    {
        // PERBAIKAN: Gunakan permission.
        // Jika punya permission 'manage all courses' (seperti Super Admin), bisa edit semua kursus.
        if ($user->hasPermissionTo('manage all courses')) {
            return true;
        }
        // Jika hanya punya 'manage own courses' (seperti Instruktur), hanya bisa edit kursus miliknya sendiri.
        if ($user->hasPermissionTo('manage own courses')) {
            return $user->id === $course->user_id;
        }
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     * Metode ini untuk menampilkan tombol "Hapus".
     */
    public function delete(User $user, Course $course): bool
    {
        // Logikanya sama persis dengan update.
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
        // Hanya Super Admin yang bisa restore.
        return $user->hasPermissionTo('manage all courses');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Course $course): bool
    {
        // Hanya Super Admin yang bisa force delete.
        return $user->hasPermissionTo('manage all courses');
    }
}
