<?php

namespace App\Policies;

use App\Models\Quiz;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class QuizPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Semua user bisa melihat daftar kuis (terutama untuk admin/instruktur di manajemen kuis)
        return $user->isAdmin() || $user->isInstructor() || $user->isParticipant();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Quiz $quiz): bool
    {
        // Semua user yang sudah login bisa melihat detail kuis (akan ada otorisasi lebih lanjut di controller untuk memulai/mengerjakan kuis)
        return $user->isAdmin() || $user->isInstructor() || $user->isParticipant();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Hanya admin atau instruktur yang bisa membuat kuis
        return $user->isAdmin() || $user->isInstructor();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Quiz $quiz): bool
    {
        // Admin bisa update semua kuis
        // Instruktur hanya bisa update kuis yang dia buat
        return $user->isAdmin() || ($user->isInstructor() && $user->id === $quiz->user_id);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Quiz $quiz): bool
    {
        // Admin bisa delete semua kuis
        // Instruktur hanya bisa delete kuis yang dia buat
        return $user->isAdmin() || ($user->isInstructor() && $user->id === $quiz->user_id);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Quiz $quiz): bool
    {
        return $user->isAdmin(); // Hanya admin yang bisa restore (jika menggunakan soft deletes)
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Quiz $quiz): bool
    {
        return $user->isAdmin(); // Hanya admin yang bisa force delete
    }
}